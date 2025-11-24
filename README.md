# prueba_pacificHealth

## Flujo de trabajo seguido durante la prueba.
1. Análisis de requerimientos del problema: entendimiento de lo que se solicita hacer para desarrollar correctamente la prueba.
2. Diagramado del flujo de la información: entendimiento básico del flujo de la información si fuera un sistema real para entender la forma en la que opera la aplicación.
3. Planteamiento de los campos faltantes y modelo relacional: al no contar con la información de la base de datos completa, había que investigar la función de cada tabla para intuir los campos faltantes y convertirlo en un modelo relacional.
4. Creación de la base de datos: una vez planteado el diagrama de la base de datos y sus relaciones, se procede a crear el script SQL con base al modelo.
5. Creación del proyecto: se crea el proyecto en las tecnologías recomendadas (PHP, Bootstrap, HTML, JS o JQuery); se crea con PHP puro por la curva de aprendizaje de Symfony y el tiempo dado pero con la arquitectura MVC que maneja un proyecto de Symfony.

## Flujo de la información
Flujo de la información:
1. Se puede crear una cotización sin estar registrado.
2. El médico puede prescribir medicamento o procedimientos.

    2.1. El sistema puede convertir una cotización en una orden médica.
3. El paciente es admitido en el centro médico.
4. Se sigue el flujo de admisión
	- Registro
	- Triage
	- Consulta
	- Laboratorio
	- Facturación
	- Alta

    4.1. Cuando el paciente pasa a consulta se genera una cita buscando en la agenda del médico.
        - Busca un slot en la agenda del médico.
        - Genera un appointment.
        - Asigna el paciente a un slot.

## Módulos
Con el entendimiento básico del flujo, se pueden intuir algunos módulos que podría tener el sistema como:
1. Módulo comercial: se realizan cotizaciones a terceros que no necesariamente están registrados.
2. Módulo de órdenes médicas: se puede crear una orden médica desde cero o convertir una cotización en una orden.
3. Módulo de admisiones: se registra y da ingreso a un nuevo cliente al centro médico.
4. Generación de consultas: después de admitido, un cliente puede pasar a consulta médica que busca en la agenda del doctor, un espacio para ser atendido.

## Tablas:
Cotización y líneas de cotización:
- com_quotation
- com_quotation_line

Orden de medicamentos:
- cnt_medical_order_medicament_quotation
- cnt_medical_order_medicament

Flujo de admisión y relación con la cita médica:
- cnt_medical_order
- adm_admission_flow
- adm_admission
- adm_admission_appointment
- sch_workflow_slot_assigned
- sch_slot_assigned
- gbl_entity

Profesional de salud y horario de la cita:
- sch_slot
- sch_event
- sch_calendar

## Script SQL de la base de datos:
![Diagrama relacional de la base de datos.](Diagrama%20relacional.jpg)

Script SQL con base al diagrama
```
-- 1. TABLA DE ENTIDADES (pacientes / médicos)
CREATE TABLE gbl_entity (
    gbl_entity_id SERIAL PRIMARY KEY,
    nombre          VARCHAR(100),
    apellido        VARCHAR(100),
    identificacion  VARCHAR(50),
    numero_tel      VARCHAR(50)
);

-- 2. TABLA DE MEDICAMENTOS
CREATE TABLE medicamento (
    medicamento_id SERIAL PRIMARY KEY,
    descripcion VARCHAR(255)
);

-- 3. COTIZACIÓN Y LÍNEAS
CREATE TABLE com_quotation (
    com_quotation_id SERIAL PRIMARY KEY,
    numero VARCHAR(50),
    fecha DATE
);

CREATE TABLE com_quotation_line (
    com_quotation_line_id SERIAL PRIMARY KEY,
    com_quotation_id INT NOT NULL,
    descripcion VARCHAR(255),
    valor NUMERIC(12,2),
    cantidad INT,
    FOREIGN KEY (com_quotation_id) REFERENCES com_quotation(com_quotation_id)
);

-- 4. ORDEN MÉDICA Y SUS MEDICAMENTOS
CREATE TABLE cnt_medical_order (
    cnt_medical_order_id SERIAL PRIMARY KEY,
    adm_admission_flow_id INT,
    fecha TIMESTAMP
);

CREATE TABLE cnt_medical_order_medicament (
    cnt_medical_order_medicament_id SERIAL PRIMARY KEY,
    cnt_medical_order_id INT NOT NULL,
    medicamento_id INT NOT NULL,
    cantidad INT,
    FOREIGN KEY (cnt_medical_order_id) REFERENCES cnt_medical_order(cnt_medical_order_id),
    FOREIGN KEY (medicamento_id) REFERENCES medicamento(medicamento_id)
);

CREATE TABLE cnt_medical_order_medicament_quotation (
    cnt_medical_order_medicament_quotation_id SERIAL PRIMARY KEY,
    com_quotation_line_id INT NOT NULL,
    cnt_medical_order_medicament_id INT NOT NULL,
    cantidad INT,
    dosis VARCHAR(255),
    FOREIGN KEY (com_quotation_line_id) REFERENCES com_quotation_line(com_quotation_line_id),
    FOREIGN KEY (cnt_medical_order_medicament_id) REFERENCES cnt_medical_order_medicament(cnt_medical_order_medicament_id)
);

-- 5. ADMISIÓN Y FLUJO DE ADMISIÓN
CREATE TABLE adm_admission (
    adm_admission_id SERIAL PRIMARY KEY,
    gbl_entity_id INT NOT NULL,
    FOREIGN KEY (gbl_entity_id) REFERENCES gbl_entity(gbl_entity_id)
);

CREATE TABLE adm_admission_flow (
    adm_admission_flow_id SERIAL PRIMARY KEY,
    adm_admission_id INT NOT NULL,
    descripcion VARCHAR(255),
    estatus VARCHAR(50),
    FOREIGN KEY (adm_admission_id) REFERENCES adm_admission(adm_admission_id)
);

ALTER TABLE cnt_medical_order
ADD CONSTRAINT fk_medical_order_flow
    FOREIGN KEY (adm_admission_flow_id)
    REFERENCES adm_admission_flow(adm_admission_flow_id);

-- 6. CITAS (Appointment)
CREATE TABLE sch_workflow_slot_assigned (
    sch_workflow_slot_assigned_id SERIAL PRIMARY KEY,
    sch_slot_assigned_id INT
);

CREATE TABLE adm_admission_appointment (
    adm_admission_appointment_id SERIAL PRIMARY KEY,
    adm_admission_id INT NOT NULL,
    sch_workflow_slot_assigned_id INT NOT NULL,
    FOREIGN KEY (adm_admission_id) REFERENCES adm_admission(adm_admission_id),
    FOREIGN KEY (sch_workflow_slot_assigned_id) REFERENCES sch_workflow_slot_assigned(sch_workflow_slot_assigned_id)
);

-- 7. ASIGNACIÓN DE SLOTS A PACIENTES
CREATE TABLE sch_slot (
    sch_slot_id SERIAL PRIMARY KEY,
    init_time TIME,
    end_time TIME,
    sch_event_id INT
);

CREATE TABLE sch_slot_assigned (
    sch_slot_assigned_id SERIAL PRIMARY KEY,
    gbl_entity_id INT NOT NULL,
    sch_slot_id INT NOT NULL,
    FOREIGN KEY (gbl_entity_id) REFERENCES gbl_entity(gbl_entity_id),
    FOREIGN KEY (sch_slot_id) REFERENCES sch_slot(sch_slot_id)
);

ALTER TABLE sch_workflow_slot_assigned
ADD CONSTRAINT fk_workflow_slot
    FOREIGN KEY (sch_slot_assigned_id)
    REFERENCES sch_slot_assigned(sch_slot_assigned_id);

-- 8. EVENTOS Y CALENDARIO DEL MÉDICO
CREATE TABLE sch_calendar (
    sch_calendar_id SERIAL PRIMARY KEY,
    gbl_entity_id INT NOT NULL,
    FOREIGN KEY (gbl_entity_id) REFERENCES gbl_entity(gbl_entity_id)
);

CREATE TABLE sch_event (
    sch_event_id SERIAL PRIMARY KEY,
    init_date DATE,
    end_date DATE,
    sch_calendar_id INT NOT NULL,
    FOREIGN KEY (sch_calendar_id) REFERENCES sch_calendar(sch_calendar_id)
);

ALTER TABLE sch_slot
ADD CONSTRAINT fk_slot_event
    FOREIGN KEY (sch_event_id) REFERENCES sch_event(sch_event_id);
```

## Datos de prueba:
```
-- PACIENTE
INSERT INTO gbl_entity (nombre, apellido, identificacion, numero_tel) VALUES ('Juan', 'Gómez', '12345678', '3001234567');
-- MÉDICO
INSERT INTO gbl_entity (nombre, apellido, identificacion, numero_tel) VALUES ('Laura', 'Narvaez', '99887766', '3015678901');
INSERT INTO medicamento (descripcion) VALUES ('Amoxicilina 500mg'),('Ibuprofeno 400mg'),('Paracetamol 500mg');
INSERT INTO com_quotation (numero, fecha) VALUES ('COT-001', '2025-02-01');
INSERT INTO com_quotation_line (com_quotation_id, descripcion, valor, cantidad) VALUES (1, 'Consulta médica general', 60000, 1), (1, 'Amoxicilina 500mg', 15000, 1);
INSERT INTO adm_admission (gbl_entity_id) VALUES (1);
INSERT INTO adm_admission_flow (adm_admission_id, descripcion, estatus) VALUES (1, 'Registro', 'completado'), (1, 'Triage', 'completado'), (1, 'Consulta', 'en_progreso');
INSERT INTO cnt_medical_order (adm_admission_flow_id, fecha) VALUES (3, '2025-02-01 10:00:00');
INSERT INTO cnt_medical_order_medicament (cnt_medical_order_id, medicamento_id, cantidad) VALUES (1, 1, 1), (1, 2, 1);
INSERT INTO cnt_medical_order_medicament_quotation (com_quotation_line_id, cnt_medical_order_medicament_id, cantidad, dosis) VALUES (2, 1, 1, '1 tableta cada 8 horas');
INSERT INTO sch_calendar (gbl_entity_id) VALUES (2);
INSERT INTO sch_event (init_date, end_date, sch_calendar_id) VALUES ('2025-02-01', '2025-02-01', 1);
INSERT INTO sch_slot (init_time, end_time, sch_event_id) VALUES ('09:00', '09:30', 1),('09:30', '10:00', 1),('10:00', '10:30', 1);
INSERT INTO sch_slot_assigned (gbl_entity_id, sch_slot_id) VALUES (1, 3);
INSERT INTO sch_workflow_slot_assigned (sch_slot_assigned_id) VALUES (1);
INSERT INTO adm_admission_appointment (adm_admission_id, sch_workflow_slot_assigned_id) VALUES (1, 10);
```
## Consulta solicitada en la prueba
Para esta consulta se creó una función en postgresql, decidí no "quemar" la consulta en el código porque se me hace poco práctico y personalmente no me gusta, en especial por ser una consulta muy compleja. Por esto se decidió crear una función (o Stored Procedure en MySQL).

Nombre: get_quotationinfo

Se llama en quotation.php de la forma 'SELECT * FROM get_quotationinfo(:id)'
```
BEGIN
    RETURN QUERY
    SELECT
        p.nombre AS paciente_nombre,
        p.apellido AS paciente_apellido,
        p.identificacion AS paciente_identificacion,
        prof.nombre AS profesional_nombre,
        prof.apellido AS profesional_apellido,
        se.init_date AS fecha_cita,
        ss.init_time AS hora_inicio,
        ss.end_time AS hora_fin
    FROM com_quotation cq
    JOIN com_quotation_line cql ON cql.com_quotation_id = cq.com_quotation_id
    JOIN cnt_medical_order_medicament_quotation mmq ON mmq.com_quotation_line_id = cql.com_quotation_line_id
    JOIN cnt_medical_order_medicament mom ON mom.cnt_medical_order_medicament_id = mmq.cnt_medical_order_medicament_id
    JOIN cnt_medical_order mo ON mo.cnt_medical_order_id = mom.cnt_medical_order_id
    JOIN adm_admission_flow af ON af.adm_admission_flow_id = mo.adm_admission_flow_id
    JOIN adm_admission adm ON adm.adm_admission_id = af.adm_admission_id
    JOIN gbl_entity p ON p.gbl_entity_id = adm.gbl_entity_id
    JOIN adm_admission_appointment app ON app.adm_admission_id = adm.adm_admission_id
    JOIN sch_workflow_slot_assigned wsa ON wsa.sch_workflow_slot_assigned_id = app.sch_workflow_slot_assigned_id
    JOIN sch_slot_assigned ssa ON ssa.sch_slot_assigned_id = wsa.sch_slot_assigned_id
    JOIN sch_slot ss ON ss.sch_slot_id = ssa.sch_slot_id
    JOIN sch_event se ON se.sch_event_id = ss.sch_event_id
    JOIN sch_calendar sc ON sc.sch_calendar_id = se.sch_calendar_id
    JOIN gbl_entity prof ON prof.gbl_entity_id = sc.gbl_entity_id
    WHERE cq.com_quotation_id = p_quotation_id;
END;
```

## NOTAS:
Para el proyecto se usó pgadmin4 como motor de base de datos, para poder usarlo (en caso de querer levantar el proyecto) se debe ir a la carpeta donde se tenga guardado XAMPP y, en el archivo php.ini, de deben descomentar las lineas:
- extension=pdo_pgsql
- extension=pgsql

para que el proyecto pueda detectar el driver.