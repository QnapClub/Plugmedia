----------------------------------------
-- UPGRADE SQL TO PLUGMEDIA 2.4
----------------------------------------
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;


-----------------------------------
-- UPDATE SHEMA INFO
-----------------------------------
UPDATE sys_conf_settings SET conf_default = '2.4' where conf_key = 'DATABASE_SCHEMA_VERSION';


-----------------------------------
-- UPDATE CONFIG
-----------------------------------

UPDATE sys_conf_settings SET conf_default = 'YTo2OntzOjI6ImZyIjtzOjg6IkZyYW7nYWlzIjtzOjI6ImVuIjtzOjc6IkVuZ2xpc2giO3M6MjoiZGUiO3M6NzoiRGV1dHNjaCI7czoyOiJubCI7czoxMDoiTmVkZXJsYW5kcyI7czoyOiJlcyI7czo3OiJFc3Bh8W9sIjtzOjI6InR3IjtzOjc6IkNoaW5lc2UiO30=' where conf_key = 'AVAILABLE_LANG';
UPDATE sys_conf_settings SET conf_default = 'YTo0OntpOjA7czozOiIzZ3AiO2k6MTtzOjU6ImF2Y2hkIjtpOjI7czozOiJta3YiO2k6MztzOjQ6IndyYXAiO30=' where conf_key = 'EXTENSION_MOV';
UPDATE sys_conf_settings SET conf_default = 'YToxMDp7aTowO3M6NDoibXBlZyI7aToxO3M6MzoibW92IjtpOjI7czozOiJhdmkiO2k6MztzOjM6Im1wNCI7aTo0O3M6MzoibTR2IjtpOjU7czozOiJtcGciO2k6NjtzOjI6InJtIjtpOjc7czo0OiJybXZiIjtpOjg7czozOiJ3bXYiO2k6OTtzOjM6ImZsdiI7fQ==' where conf_key='EXTENSION_MOV_DISPLAYABLE';




------------------------------------
-- ADD COLUMN TO FILES
------------------------------------
ALTER TABLE files ADD COLUMN mobile_version text;



------------------------------------
-- ADD NEW CONFIG
------------------------------------
INSERT INTO sys_conf_settings (conf_group, conf_key, conf_value , conf_default,enable) VALUES ( 0, 'SMALLTHUMB_WIDTH', '', '100', 1);
INSERT INTO sys_conf_settings (conf_group,conf_key,conf_value,conf_default,enable) VALUES ( 0, 'SMALLTHUMB_HEIGHT', '', '100', 1);


------------------------------------
-- NEW TABLE FOR MOVIE EXTRACT
------------------------------------
CREATE TABLE file_movie (file_id integer UNIQUE, status integer, information character varying);


ALTER TABLE public.file_movie OWNER TO plugmedia;
ALTER TABLE ONLY file_movie
    ADD CONSTRAINT file_movie_file_id_key UNIQUE (file_id);
ALTER TABLE ONLY file_movie
    ADD CONSTRAINT file_movie_fileid FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE;
