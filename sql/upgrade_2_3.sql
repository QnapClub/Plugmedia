----------------------------------------
-- UPGRADE SQL TO PLUGMEDIA 2.3
----------------------------------------
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;


---------------------------------------
-- SUPPORT SMART NAME & DESCRIPTION
---------------------------------------
ALTER TABLE files ADD COLUMN smart_name varchar(100);
ALTER TABLE files ADD COLUMN smart_description text;
ALTER TABLE directory ADD COLUMN smart_name varchar(100);
ALTER TABLE directory ADD COLUMN smart_description text;


-----------------------------------
-- UPDATE SHEMA INFO
-----------------------------------
UPDATE sys_conf_settings SET conf_default = '2.3' where conf_key = 'DATABASE_SCHEMA_VERSION';



-----------------------------------
-- TAGS
-----------------------------------
CREATE TABLE tags (
    id integer NOT NULL,
    value character varying(100) NOT NULL,
    date time without time zone DEFAULT now(),
    soundex character varying(10)
);

ALTER TABLE public.tags OWNER TO plugmedia;

CREATE SEQUENCE tags_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER TABLE public.tags_id_seq OWNER TO plugmedia;
ALTER SEQUENCE tags_id_seq OWNED BY tags.id;
ALTER TABLE tags ALTER COLUMN id SET DEFAULT nextval('tags_id_seq'::regclass);
ALTER TABLE ONLY tags
    ADD CONSTRAINT tags_pkey PRIMARY KEY (id);
ALTER TABLE ONLY tags
    ADD CONSTRAINT tags_value_key UNIQUE (value);

 


-----------------------------------
-- TAGS LINK
-----------------------------------
CREATE TABLE tags_files (
    tag_id integer NOT NULL,
    file_id integer NOT NULL,	
    additional_info text	
);

ALTER TABLE public.tags_files OWNER TO plugmedia;
ALTER TABLE ONLY tags_files
    ADD CONSTRAINT tags_files_primarykey PRIMARY KEY (tag_id, file_id);
ALTER TABLE ONLY tags_files
    ADD CONSTRAINT tags_files_fk_file_id FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE;
ALTER TABLE ONLY tags_files
    ADD CONSTRAINT tags_files_fk_tag_id FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE;


-----------------------------------
-- ADD NEW USER CONFIG
-----------------------------------
ALTER TABLE users ADD COLUMN can_manage_metadata smallint DEFAULT 0 NOT NULL;


----------------------------------------------
-- UPDATE METADATA TO SUPPORT GPS LOCATION
----------------------------------------------
ALTER TABLE metadata_exif ADD COLUMN gpslatituderef character varying(5);
ALTER TABLE metadata_exif ADD COLUMN gpslatitude character varying(30);
ALTER TABLE metadata_exif ADD COLUMN gpslongituderef character varying(5);
ALTER TABLE metadata_exif ADD COLUMN gpslongitude character varying(30);




