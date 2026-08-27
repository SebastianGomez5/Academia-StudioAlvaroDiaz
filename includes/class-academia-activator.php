<?php
/**
 * Fired during plugin activation
 */

if (!defined('ABSPATH')) {
    exit;
}

class Academia_Activator {

    public static function activate() {
        self::create_tables();
        self::seed_initial_data();
        self::register_default_options();
    }

    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // 1. Courses Table
        $table_courses = $wpdb->prefix . 'academia_courses';
        $sql_courses = "CREATE TABLE $table_courses (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            slug varchar(50) NOT NULL,
            code varchar(50) NOT NULL,
            short_name varchar(100) NOT NULL,
            title varchar(255) NOT NULL,
            icon varchar(20) DEFAULT '🌱',
            color varchar(20) DEFAULT '#5B4FBE',
            description text,
            wc_product_id bigint(20) DEFAULT NULL,
            is_free_default tinyint(1) DEFAULT 0,
            sort_order int(11) DEFAULT 0,
            PRIMARY KEY  (id),
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql_courses);

        // 2. Modules Table
        $table_modules = $wpdb->prefix . 'academia_modules';
        $sql_modules = "CREATE TABLE $table_modules (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            course_id bigint(20) NOT NULL,
            title varchar(255) NOT NULL,
            tag varchar(100) DEFAULT '',
            color varchar(20) DEFAULT '#5B4FBE',
            bg_light varchar(20) DEFAULT '#F0EEFD',
            sort_order int(11) DEFAULT 0,
            estimated_time varchar(50) DEFAULT '45 min',
            difficulty varchar(50) DEFAULT 'Media',
            summary text,
            target_deliverable varchar(255) DEFAULT '',
            target_tool varchar(255) DEFAULT '',
            PRIMARY KEY  (id),
            KEY course_id (course_id)
        ) $charset_collate;";
        dbDelta($sql_modules);

        // 3. Lessons Table
        $table_lessons = $wpdb->prefix . 'academia_lessons';
        $sql_lessons = "CREATE TABLE $table_lessons (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            module_id bigint(20) NOT NULL,
            lesson_code varchar(50) DEFAULT '',
            title varchar(255) NOT NULL,
            type varchar(100) DEFAULT '🎬 Masterclass en Vídeo',
            duration varchar(50) DEFAULT '12 min',
            what_you_will_learn text,
            business_utility text,
            video_title varchar(255) DEFAULT '',
            video_url varchar(255) DEFAULT '',
            reading_text longtext,
            sort_order int(11) DEFAULT 0,
            PRIMARY KEY  (id),
            KEY module_id (module_id)
        ) $charset_collate;";
        dbDelta($sql_lessons);

        // 4. Activities Table
        $table_activities = $wpdb->prefix . 'academia_activities';
        $sql_activities = "CREATE TABLE $table_activities (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            module_id bigint(20) NOT NULL,
            lesson_id bigint(20) DEFAULT NULL,
            title varchar(255) NOT NULL,
            activity_type varchar(50) DEFAULT 'task',
            options_json text,
            correct_answer text,
            PRIMARY KEY  (id),
            KEY module_id (module_id)
        ) $charset_collate;";
        dbDelta($sql_activities);

        // 5. Tools Table
        $table_tools = $wpdb->prefix . 'academia_tools';
        $sql_tools = "CREATE TABLE $table_tools (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            course_id bigint(20) NOT NULL,
            module_id bigint(20) DEFAULT NULL,
            name varchar(255) NOT NULL,
            platform varchar(100) DEFAULT 'PDF editable',
            phase varchar(100) DEFAULT 'Semana 1',
            priority varchar(20) DEFAULT '🟡',
            difficulty varchar(50) DEFAULT 'Media',
            estimated_time varchar(50) DEFAULT '30 min',
            feeds_deliverable varchar(255) DEFAULT '',
            counts_for_cert tinyint(1) DEFAULT 1,
            status varchar(50) DEFAULT 'todo',
            url varchar(255) DEFAULT '#',
            objective text,
            PRIMARY KEY  (id),
            KEY course_id (course_id)
        ) $charset_collate;";
        dbDelta($sql_tools);

        // 6. Enrollments Table
        $table_enrollments = $wpdb->prefix . 'academia_enrollments';
        $sql_enrollments = "CREATE TABLE $table_enrollments (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            course_id bigint(20) NOT NULL,
            wc_order_id bigint(20) DEFAULT NULL,
            status varchar(50) DEFAULT 'active',
            enrolled_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY course_id (course_id)
        ) $charset_collate;";
        dbDelta($sql_enrollments);

        // 7. User Progress Table
        $table_progress = $wpdb->prefix . 'academia_user_progress';
        $sql_progress = "CREATE TABLE $table_progress (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            lesson_id bigint(20) NOT NULL,
            completed tinyint(1) DEFAULT 0,
            read_completed tinyint(1) DEFAULT 0,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY user_lesson (user_id, lesson_id)
        ) $charset_collate;";
        dbDelta($sql_progress);

        // 8. Deliverables Submissions Table
        $table_deliverables = $wpdb->prefix . 'academia_deliverables';
        $sql_deliverables = "CREATE TABLE $table_deliverables (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            course_id bigint(20) NOT NULL,
            module_id bigint(20) NOT NULL,
            deliverable_name varchar(255) NOT NULL,
            submission_type varchar(50) DEFAULT 'link',
            submission_url text,
            file_path text,
            status varchar(50) DEFAULT 'pending',
            mentor_feedback text,
            reviewed_by bigint(20) DEFAULT NULL,
            reviewed_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_course_mod (user_id, course_id, module_id)
        ) $charset_collate;";
        dbDelta($sql_deliverables);

        // 9. Studio Data Table
        $table_studio = $wpdb->prefix . 'academia_studio_data';
        $sql_studio = "CREATE TABLE $table_studio (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            course_id bigint(20) NOT NULL,
            studio_type varchar(50) NOT NULL,
            data_json longtext,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_course_studio (user_id, course_id, studio_type)
        ) $charset_collate;";
        dbDelta($sql_studio);

        // 10. Impact Surveys Table
        $table_impact = $wpdb->prefix . 'academia_impact_surveys';
        $sql_impact = "CREATE TABLE $table_impact (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            course_id bigint(20) NOT NULL,
            responses_json longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_course_impact (user_id, course_id)
        ) $charset_collate;";
        dbDelta($sql_impact);
    }

    private static function seed_initial_data() {
        global $wpdb;

        $courses_table = $wpdb->prefix . 'academia_courses';
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$courses_table}");
        if ($count > 0) {
            return; // Already seeded
        }

        // Seed 8 Courses
        $courses = array(
            array(
                'slug' => 'm0',
                'code' => 'MÓDULO 0',
                'short_name' => '0 · Iniciación',
                'title' => '0 · INICIACIÓN - DEL CAOS A LA ESTRUCTURA',
                'icon' => '🌱',
                'color' => '#10B981',
                'description' => 'Alineación inicial, hábitos de aprendizaje profundo y preparación de la estructura base del líder.',
                'wc_product_id' => 1,
                'is_free_default' => 1,
                'sort_order' => 0
            ),
            array(
                'slug' => 'c1',
                'code' => 'MÓDULO 1',
                'short_name' => '1 · Cuerpo',
                'title' => '1 · DEL CAOS AL BIENESTAR',
                'icon' => '🔋',
                'color' => '#0D9488',
                'description' => 'Rendimiento físico, regulación del estrés cognitivo, energía ejecutiva y longevidad del líder.',
                'wc_product_id' => 2,
                'is_free_default' => 0,
                'sort_order' => 1
            ),
            array(
                'slug' => 'c2',
                'code' => 'MÓDULO 2',
                'short_name' => '2 · Mente',
                'title' => '2 · DEL CAOS A LA IDENTIDAD',
                'icon' => '🧠',
                'color' => '#5B4FBE',
                'description' => 'Estructura mental, gestión de sesgos neurocognitivos, serenidad y toma de decisiones sin ruido.',
                'wc_product_id' => 8,
                'is_free_default' => 0,
                'sort_order' => 2
            ),
            array(
                'slug' => 'c3',
                'code' => 'MÓDULO 3',
                'short_name' => '3 · Dinero',
                'title' => '3 · DEL CAOS FINANCIERO A LA LIBERTAD',
                'icon' => '📈',
                'color' => '#0284C7',
                'description' => 'Control de margen, flujo de caja libre, valoración de la compañía y rentabilidad sostenible.',
                'wc_product_id' => 3,
                'is_free_default' => 0,
                'sort_order' => 3
            ),
            array(
                'slug' => 'c4',
                'code' => 'MÓDULO 4',
                'short_name' => '4 · Ventas',
                'title' => '4 · DEL CAOS A LA VENTA',
                'icon' => '📣',
                'color' => '#D97706',
                'description' => 'Marketing de identidad, posicionamiento de autoridad, propuesta de valor y captación de clientes.',
                'wc_product_id' => 4,
                'is_free_default' => 0,
                'sort_order' => 4
            ),
            array(
                'slug' => 'c5',
                'code' => 'MÓDULO 5',
                'short_name' => '5 · Sistemas o Conexiones',
                'title' => '5 · DEL CAOS DIGITAL AL SISTEMA AUTOMATIZADO',
                'icon' => '⚙️',
                'color' => '#059669',
                'description' => 'Sistematización de nodos operacionales, integración de procesos e independencia del fundador.',
                'wc_product_id' => 5,
                'is_free_default' => 0,
                'sort_order' => 5
            ),
            array(
                'slug' => 'c6',
                'code' => 'MÓDULO 6',
                'short_name' => '6 · Liderazgo',
                'title' => '6 · DEL CAOS AL LIDERAZGO',
                'icon' => '🤝',
                'color' => '#7C3AED',
                'description' => 'Gobernanza de equipos, liderazgo de mandos medios, procesos justos y resolución de fricciones.',
                'wc_product_id' => 6,
                'is_free_default' => 0,
                'sort_order' => 6
            ),
            array(
                'slug' => 'c7',
                'code' => 'MÓDULO 7',
                'short_name' => '7 · Legado',
                'title' => '7 · DE LA ESTRUCTURA AL LEGADO',
                'icon' => '🚀',
                'color' => '#DC2626',
                'description' => 'Estrategias de expansión tectónica, escalabilidad, trascendencia institucional y visión a largo plazo.',
                'wc_product_id' => 7,
                'is_free_default' => 0,
                'sort_order' => 7
            ),
        );

        foreach ($courses as $c) {
            $wpdb->insert($courses_table, $c);
        }

        // Seed Course 0 (Iniciación)
        $course0_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$courses_table} WHERE slug = %s", 'm0'));
        if ($course0_id) {
            self::seed_course_0_content($course0_id);
        }

        // Get ID for Course 2 (Del Caos a la Identidad)
        $course2_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$courses_table} WHERE slug = %s", 'c2'));
        if ($course2_id) {
            self::seed_course_2_content($course2_id);
        }
    }

    private static function seed_course_0_content($course_id) {
        global $wpdb;
        $modules_table = $wpdb->prefix . 'academia_modules';
        $lessons_table = $wpdb->prefix . 'academia_lessons';
        $tools_table   = $wpdb->prefix . 'academia_tools';
        $activities_table = $wpdb->prefix . 'academia_activities';

        // Module 1 of Course 0
        $wpdb->insert($modules_table, array(
            'course_id' => $course_id,
            'title' => 'Fundamentos de la Estructura de Negocios',
            'tag' => 'A · Alineación',
            'color' => '#10B981',
            'bg_light' => '#ECFDF5',
            'sort_order' => 1,
            'estimated_time' => '30 min',
            'difficulty' => 'Baja',
            'summary' => 'Bienvenido a la Academia Tectónica. En este módulo de iniciación entenderás los principios fundamentales de la arquitectura de negocios y el mapa de 5 etapas para liberar tiempo del fundador.',
            'target_deliverable' => 'Diagnóstico Operativo Inicial (Pilar A)',
            'target_tool' => 'Filtro del Caos en Studio'
        ));
        $mod0_1 = $wpdb->insert_id;

        $wpdb->insert($lessons_table, array(
            'module_id' => $mod0_1,
            'lesson_code' => 'L0.1',
            'title' => 'Bienvenida y Manifiesto del Estructurador',
            'type' => '🎬 Masterclass en Vídeo',
            'duration' => '10 min',
            'what_you_will_learn' => 'Comprender por qué los negocios colapsan bajo su propio éxito y cómo la arquitectura previene el agotamiento.',
            'business_utility' => 'Sentar las bases para la delegación y la sistematización autónoma de tu empresa.',
            'video_title' => 'Vídeo Introductorio: Del Caos a la Estructura',
            'reading_text' => 'Un negocio sin arquitectura depende permanentemente de la energía reactiva de su fundador. La estructuración no es burocracia: es libertad operativa.',
            'sort_order' => 1
        ));

        $wpdb->insert($lessons_table, array(
            'module_id' => $mod0_1,
            'lesson_code' => 'L0.2',
            'title' => 'El Mapa de las 5 Etapas de la Libertad Operativa',
            'type' => '💻 Video Demo en Vivo',
            'duration' => '15 min',
            'what_you_will_learn' => 'Recorrer la ruta desde el diagnóstico de fallas críticas hasta la trascendencia del legado.',
            'business_utility' => 'Definir el orden exacto de prioridades antes de implementar herramientas tecnológicas.',
            'video_title' => 'Masterclass: Ruta de las 5 Etapas',
            'reading_text' => 'Automatizar el caos solo genera caos automatizado. Primero estructuramos los procesos internos, luego insertamos la tecnología.',
            'sort_order' => 2
        ));

        $wpdb->insert($tools_table, array(
            'course_id' => $course_id,
            'name' => 'Checklist de Diagnóstico Operativo',
            'platform' => 'PDF editable / Studio',
            'phase' => 'Semana 1',
            'priority' => '🔴',
            'difficulty' => 'Baja',
            'estimated_time' => '20 min',
            'feeds_deliverable' => 'Diagnóstico Operativo Inicial (Pilar A)',
            'counts_for_cert' => 1,
            'status' => 'done',
            'url' => '#',
            'objective' => 'Auditar las fugas de tiempo del director en 10 puntos clave.'
        ));
    }

    private static function seed_course_2_content($course_id) {
        global $wpdb;
        $modules_table = $wpdb->prefix . 'academia_modules';
        $lessons_table = $wpdb->prefix . 'academia_lessons';
        $tools_table   = $wpdb->prefix . 'academia_tools';
        $activities_table = $wpdb->prefix . 'academia_activities';

        // Module 1
        $wpdb->insert($modules_table, array(
            'course_id' => $course_id,
            'title' => 'Fundamentos Neurocognitivos',
            'tag' => 'A · Alineación',
            'color' => '#5B4FBE',
            'bg_light' => '#F0EEFD',
            'sort_order' => 1,
            'estimated_time' => '45 min',
            'difficulty' => 'Baja',
            'summary' => 'En este módulo aprenderás a reprogramar los patrones de automatismo mental ante la incertidumbre, sustituyendo el ruido y el estrés por claridad estratégica para tomar decisiones con la mente serena.',
            'target_deliverable' => 'Tracker de Neuroretos (Pilar B)',
            'target_tool' => 'Tracker de Neuroretos en Studio'
        ));
        $mod1_id = $wpdb->insert_id;

        // Lessons for Module 1
        $m1_lessons = array(
            array('L1.1', 'De la Mente Reactiva al Liderazgo Consciente', '🎬 Masterclass en Vídeo', '12 min', 'Comprender los 3 circuitos neurocognitivos que se activan bajo presión de caja.', 'Evitar tomar decisiones apresuradas o impulsivas durante picos de estrés en la empresa.', 'Vídeo Principal Mód 1: Arquitectura del Sesgo del Líder', 'La mente reactiva opera bajo el sistema de amenaza biológico. Cuando los números de caja muestran volatilidad, la amígdala cerebral secuestra la corteza prefrontal, anulando la visión a largo plazo. Para liderar con perspectiva, debes aprender a pausar el primer impulso automático de respuesta.'),
            array('L1.2', 'Filtra el Caos: El Protocolo 3-2-1', '💻 Video Demo en Vivo + Ejercicio', '10 min', 'Dominar la técnica de triaje mental en 3 pasos para reducir el agotamiento cognitivo.', 'Clarificar las prioridades diarias del equipo en menos de 15 minutos cada mañana.', 'Video Demo: Ejecutando el filtro 3-2-1 en tiempo real', 'El protocolo 3-2-1 divide el ruido operacional en 3 focos de caos externo, 2 variables bajo control directo y 1 micro-acción inmediata ejecutada en menos de 20 minutos. Este ritual reduce drásticamente el cortisol matutino.'),
            array('L1.3', 'Auditoría de Creencias Limitantes', '📹 Video Guía Explicativo', '15 min', 'Desmontar distorsiones cognitivas que bloquean la delegación de procesos.', 'Liberar al fundador de cuellos de botella operativos autoreferenciales.', 'Video Explicativo: Cómo minar creencias arraigadas', 'Detrás de cada cuello de botella operativo donde el fundador "debe revisarlo todo", existe una creencia no examinada de control. La auditoría exige contrastar la suposición emocional contra métricas duras.'),
            array('L1.4', 'Integración Comunitaria de Neuroretos', '🎥 Video Píldora de Hábitos', '8 min', 'Fijar el primer compromiso de hábito cognitivo mediante rendición de cuentas pública.', 'Asegurar que el cambio personal se transmita a la cultura operativa del equipo.', 'Video Guía: Ritual de hábito y rendición de cuentas', 'La rendición de cuentas compartida multiplica por cuatro la tasa de adhesión a nuevos hábitos ejecutivos. Exponer públicamente el compromiso genera un freno social natural ante las recaídas reactivas.')
        );

        $order = 1;
        foreach ($m1_lessons as $l) {
            $wpdb->insert($lessons_table, array(
                'module_id' => $mod1_id,
                'lesson_code' => $l[0],
                'title' => $l[1],
                'type' => $l[2],
                'duration' => $l[3],
                'what_you_will_learn' => $l[4],
                'business_utility' => $l[5],
                'video_title' => $l[6],
                'video_url' => 'https://youtu.be/MeKlBPHgmJ0',
                'reading_text' => $l[7],
                'sort_order' => $order++
            ));
        }

        // Activities for Module 1
        $m1_acts = array(
            'Inventario de 3 creencias limitantes',
            'Registro del 1er Neuroreto',
            'Ejercicio Filtra el Caos (3-2-1)',
            '🤝 Objetivo de Cambio Mental en comunidad'
        );
        foreach ($m1_acts as $act) {
            $wpdb->insert($activities_table, array('module_id' => $mod1_id, 'title' => $act));
        }

        // Module 2
        $wpdb->insert($modules_table, array(
            'course_id' => $course_id,
            'title' => 'Anatomía del Pensamiento',
            'tag' => 'L · Liderazgo',
            'color' => '#2E8B9A',
            'bg_light' => '#EAF6F8',
            'sort_order' => 2,
            'estimated_time' => '60 min',
            'difficulty' => 'Media',
            'summary' => 'En este módulo aprenderás a auditar el flujo de entrada, elaboración y salida de tus decisiones ejecutivas utilizando la Matriz 2x2 para eliminar sumideros de tiempo.',
            'target_deliverable' => 'Matriz de Decisiones 2x2 (Pilar A)',
            'target_tool' => 'Matriz 2x2 en Studio'
        ));
        $mod2_id = $wpdb->insert_id;

        $m2_lessons = array(
            array('L2.1', 'El Modelo E-E-S (Entrada, Elaboración, Salida)', '🎬 Masterclass en Vídeo', '14 min', 'Mapear cómo la información cruda del negocio entra y se procesa en el cerebro del líder.', 'Prevenir la parálisis por análisis y estructurar filtros de información de alta calidad.', 'Masterclass: Auditoría de Entradas y Salidas de Información', 'El modelo E-E-S concibe la mente ejecutiva como una planta de procesamiento. Si la calidad de la información de entrada es pobre o sesgada por rumores, la elaboración genera decisiones deficientes independientemente del talento directivo.'),
            array('L2.2', 'Diseño y Dominio de la Matriz 2x2', '💻 Video Demo en Vivo', '15 min', 'Clasificar cualquier iniciativa según su Esfuerzo relativo vs. su Impacto estratégico.', 'Identificar victorias rápidas (Quick Wins) y cancelar proyectos sumidero de recursos.', 'Video Tutorial: Clasificación ágil de proyectos en Canva/Studio', 'La Matriz de Impacto vs. Esfuerzo obliga a jerarquizar fríamente la cartera de proyectos. Permite erradicar las iniciativas de "Bajo Impacto y Alto Esfuerzo" que desgastan al equipo sin mover la aguja financiera.'),
            array('L2.3', 'Filtrado de los 3 Tipos de Información', '📹 Video Guía Metodológica', '10 min', 'Diferenciar entre datos duros, ruido mediático y opiniones no fundamentadas.', 'Tomar decisiones de inversión basadas en métricas reales de caja y no en intuiciones emocionales.', 'Video Explicativo: Descontaminar la mesa de toma de decisiones', 'Distinguir entre Hechos Probados, Suposiciones Probables y Opiniones Emocionales. Tomar decisiones estratégicas utilizando únicamente hechos probados minimiza los errores de inversión.'),
            array('L2.4', 'Redacción Ejecutiva con Guiones ESTRUCTURADOS', '🎥 Video Píldora Formativa', '11 min', 'Comunicar decisiones complejas al equipo sin dejar espacio a ambigüedades.', 'Reducir en un 50% las reuniones de aclaración con líderes de área.', 'Píldora Formativa: Guiones directos para comunicar cambios', 'La claridad directiva se mide por la falta de ambigüedad en la instrucción. Un guion ejecutivo estructurado define: Contexto, Decisión Adoptada, Justificación Estratégica y Expectativa Concreta de Ejecución.'),
            array('L2.5', 'Auditoría Cuantitativa del Pensamiento', '💻 Video Demo de Cuestionario', '10 min', 'Calcular el índice de efectividad de las últimas 5 decisiones importantes del trimestre.', 'Obtener la línea base cuantitativa para la evaluación de certificación.', 'Video Demo: Cómo completar el Audit de Decisiones', 'Medir retrospectivamente el resultado real contra la expectativa prevista al tomar cada decisión. Esta práctica elimina la memoria selectiva y el sesgo de retrospectiva.')
        );

        $order = 1;
        foreach ($m2_lessons as $l) {
            $wpdb->insert($lessons_table, array(
                'module_id' => $mod2_id,
                'lesson_code' => $l[0],
                'title' => $l[1],
                'type' => $l[2],
                'duration' => $l[3],
                'what_you_will_learn' => $l[4],
                'business_utility' => $l[5],
                'video_title' => $l[6],
                'reading_text' => $l[7],
                'sort_order' => $order++
            ));
        }

        // Module 3
        $wpdb->insert($modules_table, array(
            'course_id' => $course_id,
            'title' => 'Reestructuración Cognitiva',
            'tag' => 'M · Mensaje',
            'color' => '#C0454E',
            'bg_light' => '#FDF1F2',
            'sort_order' => 3,
            'estimated_time' => '50 min',
            'difficulty' => 'Alta',
            'summary' => 'En este módulo aprenderás técnicas socráticas avanzadas de minería de creencias y contención de crisis para comunicar decisiones contundentes y objetivas.',
            'target_deliverable' => 'Mapa de Sistemas de Negocio (Pilar B)',
            'target_tool' => 'Filtro del Caos en Studio'
        ));
        $mod3_id = $wpdb->insert_id;

        $m3_lessons = array(
            array('L3.1', 'Los 10 Filtros Mentales que Destruyen Empresas', '🎬 Masterclass en Vídeo', '15 min', 'Reconocer el catastrofismo, la personalización y la visión de túnel operativa.', 'Proteger la moral y objetividad de los directivos durante momentos de fricción de mercado.', 'Masterclass: Distorsiones Cognitivas en Líderes de Alto Rendimiento', 'Los filtros mentales distorsionan la lectura de la realidad del mercado. Detectar la visión de túnel cuando una métrica falla evita tomar decisiones desproporcionadas o de pánico.'),
            array('L3.2', 'El Interrogatorio Socrático de Creencias', '💻 Video Demo en Vivo', '12 min', 'Aplicar el método de 4 preguntas de validación ante bloqueos de delegación.', 'Desarmar resistencias internas antes de iniciar procesos de reestructuración corporativa.', 'Video Tutorial: Desmontando una objeción de control', 'El interrogatorio socrático examina la evidencia: ¿Es absolutamente cierto que nadie más puede hacer esta tarea? ¿Cuál es el peor escenario comprobable si delego el 80%?')
        );
        $order = 1;
        foreach ($m3_lessons as $l) {
            $wpdb->insert($lessons_table, array(
                'module_id' => $mod3_id,
                'lesson_code' => $l[0],
                'title' => $l[1],
                'type' => $l[2],
                'duration' => $l[3],
                'what_you_will_learn' => $l[4],
                'business_utility' => $l[5],
                'video_title' => $l[6],
                'reading_text' => $l[7],
                'sort_order' => $order++
            ));
        }

        // Module 4
        $wpdb->insert($modules_table, array(
            'course_id' => $course_id,
            'title' => 'Arquitectura de Hábitos Ejecutivos',
            'tag' => 'A · Arquitectura',
            'color' => '#8B5CF6',
            'bg_light' => '#F5F3FF',
            'sort_order' => 4,
            'estimated_time' => '55 min',
            'difficulty' => 'Media',
            'summary' => 'En este módulo aprenderás a diseñar un sistema de rituales y anclajes neuroconductuales para blindar tu tiempo de alta concentración.',
            'target_deliverable' => 'Plan de Acción a 90 Días (Pilar A)',
            'target_tool' => 'Tracker de Neuroretos en Studio'
        ));
        $mod4_id = $wpdb->insert_id;

        $m4_lessons = array(
            array('L4.1', 'El Bucle de Hábito Directivo', '🎬 Masterclass en Vídeo', '14 min', 'Diseñar disparadores visuales y rutinas de alta densidad cognitiva.', 'Asegurar 3 horas diarias de trabajo profundo sin interrupciones del equipo.', 'Masterclass: Anclaje de Hábitos de Alto Rendimiento', 'Un hábito directivo necesita un disparador ambiental nítido. Desconectar notificaciones y reservar bloques sagrados de 90 minutos transforma la productividad de la semana.')
        );
        $order = 1;
        foreach ($m4_lessons as $l) {
            $wpdb->insert($lessons_table, array(
                'module_id' => $mod4_id,
                'lesson_code' => $l[0],
                'title' => $l[1],
                'type' => $l[2],
                'duration' => $l[3],
                'what_you_will_learn' => $l[4],
                'business_utility' => $l[5],
                'video_title' => $l[6],
                'reading_text' => $l[7],
                'sort_order' => $order++
            ));
        }

        // Module 5
        $wpdb->insert($modules_table, array(
            'course_id' => $course_id,
            'title' => 'Integración Comunitaria y Evaluación',
            'tag' => 'E · Evaluación',
            'color' => '#10B981',
            'bg_light' => '#ECFDF5',
            'sort_order' => 5,
            'estimated_time' => '40 min',
            'difficulty' => 'Media',
            'summary' => 'En este módulo consolidarás todos los entregables requeridos para la titulación en el curso y realizarás tu rendición de cuentas final.',
            'target_deliverable' => 'Caso de Éxito y Portafolio Completo',
            'target_tool' => 'Portafolio de Graduación'
        ));
        $mod5_id = $wpdb->insert_id;

        $m5_lessons = array(
            array('L5.1', 'Presentación del Portafolio Tectónico', '🎬 Masterclass en Vídeo', '10 min', 'Estructurar los 3 pilares del portafolio final para la validación de la mentora.', 'Conseguir la titulación oficial de la Academia Tectónica.', 'Masterclass: Criterios de Aprobación del Portafolio ALMA', 'El portafolio final demuestra con hechos la transformación de la arquitectura de negocio y la autonomía mental del fundador.')
        );
        $order = 1;
        foreach ($m5_lessons as $l) {
            $wpdb->insert($lessons_table, array(
                'module_id' => $mod5_id,
                'lesson_code' => $l[0],
                'title' => $l[1],
                'type' => $l[2],
                'duration' => $l[3],
                'what_you_will_learn' => $l[4],
                'business_utility' => $l[5],
                'video_title' => $l[6],
                'reading_text' => $l[7],
                'sort_order' => $order++
            ));
        }

        // Seed Tools for Course 2
        $tools = array(
            array('Matriz de Decisiones 2x2', 'Canva template / Studio', 'Semana 1', '🔴', 'Media', '30 min', 'Matriz de Decisiones 2x2 (Pilar A)', 1, 'todo', 'https://canva.com', 'Clasificar proyectos según su impacto vs esfuerzo.'),
            array('Tracker de Neuroretos', 'Google Sheets / Studio', 'Semana 1-4', '🔴', 'Baja', '5 min/día', 'Tracker de Neuroretos (Pilar B)', 1, 'progress', 'https://sheets.google.com', 'Monitorear la consistencia de 6 hábitos cognitivos.'),
            array('Filtro del Caos 3-2-1', 'Studio interactivo', 'Semana 1', '🔴', 'Baja', '15 min/día', 'Tracker de Neuroretos (Pilar B)', 1, 'done', '#', 'Triaje mental matutino en 3 pasos.'),
            array('Inventario de Creencias Limitantes', 'PDF editable', 'Semana 1', '🔴', 'Media', '45 min', 'Caso de Éxito (Pilar C)', 1, 'done', '#', 'Identificar supuestos que frenan la delegación.'),
            array('Plan de Acción a 90 Días', 'Notion template', 'Semana 4', '🔴', 'Alta', '90 min', 'Plan de Acción a 90 Días (Pilar A)', 1, 'todo', '#', 'Hoja de ruta estratégica del próximo trimestre.'),
            array('Mapa de Sistemas de Negocio', 'Miro template', 'Semana 2', '🟡', 'Alta', '60 min', 'Mapa de Sistemas de Negocio', 1, 'progress', 'https://miro.com', 'Diagramar los nodos operacionales y cuellos de botella.')
        );

        foreach ($tools as $t) {
            $wpdb->insert($tools_table, array(
                'course_id' => $course_id,
                'name' => $t[0],
                'platform' => $t[1],
                'phase' => $t[2],
                'priority' => $t[3],
                'difficulty' => $t[4],
                'estimated_time' => $t[5],
                'feeds_deliverable' => $t[6],
                'counts_for_cert' => $t[7],
                'status' => $t[8],
                'url' => $t[9],
                'objective' => $t[10]
            ));
        }
    }

    private static function register_default_options() {
        if (!get_option('academia_mentor_name')) {
            update_option('academia_mentor_name', 'Dra. Erika Tatiana Parra');
        }
        if (!get_option('academia_mentor_avatar_url')) {
            update_option('academia_mentor_avatar_url', 'https://studioalvarodiaz.es/wp-content/uploads/2026/08/WhatsApp-Image-2026-08-27-at-5.38.59-PM.jpeg');
        }
        if (!get_option('academia_mentor_role')) {
            update_option('academia_mentor_role', 'Directora de Evaluación y mentora de estructuras ALMA');
        }
        if (!get_option('academia_mentor_avatar')) {
            update_option('academia_mentor_avatar', '👩‍🏫');
        }
        if (!get_option('academia_fluent_booking_url')) {
            update_option('academia_fluent_booking_url', 'https://alvarodiaz.com/reserva-mentoria');
        }
        if (!get_option('academia_fluent_booking_shortcode')) {
            update_option('academia_fluent_booking_shortcode', '[fluent_booking id="3"]');
        }
        if (!get_option('academia_mentor_call_price')) {
            update_option('academia_mentor_call_price', '97€');
        }
    }
}
