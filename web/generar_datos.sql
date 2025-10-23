-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS proyecto_final;
USE proyecto_final;

-- Crear la tabla
CREATE TABLE IF NOT EXISTS datos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL,
    topic VARCHAR(50) NOT NULL,
    fecha DATETIME NOT NULL,
    payload VARCHAR(8) NOT NULL,
    INDEX(usuario),
    INDEX(topic),
    INDEX(fecha)
);

-- Limpiar la tabla (opcional)
TRUNCATE TABLE datos;

DELIMITER $$

DROP PROCEDURE IF EXISTS generar_datos_3dias$$
CREATE PROCEDURE generar_datos_3dias()
BEGIN
    DECLARE dt DATETIME;
    DECLARE usuario VARCHAR(10);
    DECLARE topic VARCHAR(20);
    DECLARE ace_idx INT;
    DECLARE topic_idx INT;

    -- Comenzamos 72 horas atrás desde el momento de ejecución
    SET dt = DATE_SUB(NOW(), INTERVAL 72 HOUR);

    WHILE dt <= NOW() DO
        SET ace_idx = 1;
        WHILE ace_idx <= 3 DO
            IF ace_idx = 1 THEN SET usuario = 'ACE1'; END IF;
            IF ace_idx = 2 THEN SET usuario = 'ACE2'; END IF;
            IF ace_idx = 3 THEN SET usuario = 'ACE3'; END IF;

            SET topic_idx = 1;
            WHILE topic_idx <= 5 DO
                IF topic_idx = 1 THEN SET topic = 'frecuencia'; END IF;
                IF topic_idx = 2 THEN SET topic = 'tension'; END IF;
                IF topic_idx = 3 THEN SET topic = 'intensidad'; END IF;
                IF topic_idx = 4 THEN SET topic = 'potencia'; END IF;
                IF topic_idx = 5 THEN SET topic = 'fp'; END IF;

                -- Insertar valores aleatorios dentro de rangos típicos
                IF topic = 'frecuencia' THEN
                    INSERT INTO datos(usuario, topic, fecha, payload)
                    VALUES(usuario, topic, dt, FORMAT(49.8 + (RAND()*0.4), 2));
                ELSEIF topic = 'tension' THEN
                    INSERT INTO datos(usuario, topic, fecha, payload)
                    VALUES(usuario, topic, dt, FORMAT(218 + (RAND()*5),0));
                ELSEIF topic = 'intensidad' THEN
                    INSERT INTO datos(usuario, topic, fecha, payload)
                    VALUES(usuario, topic, dt, FORMAT(4.8 + (RAND()*0.5),2));
                ELSEIF topic = 'potencia' THEN
                    INSERT INTO datos(usuario, topic, fecha, payload)
                    VALUES(usuario, topic, dt, FORMAT(1050 + (RAND()*100),0));
                ELSEIF topic = 'fp' THEN
                    INSERT INTO datos(usuario, topic, fecha, payload)
                    VALUES(usuario, topic, dt, FORMAT(0.9 + (RAND()*0.1),2));
                END IF;

                SET topic_idx = topic_idx + 1;
            END WHILE;

            SET ace_idx = ace_idx + 1;
        END WHILE;

        -- Avanzamos 15 minutos
        SET dt = DATE_ADD(dt, INTERVAL 15 MINUTE);
    END WHILE;
END$$

DELIMITER ;

-- Ejecutar el procedimiento para generar los datos
CALL generar_datos_3dias();

-- Opcional: eliminar el procedimiento si no se va a usar más
DROP PROCEDURE generar_datos_3dias;
