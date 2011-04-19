----------------------------------------
-- UPGRADE SQL TO PLUGMEDIA 2.2.1
----------------------------------------
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;


--------------------------------------
-- TABLE DIRECTORY FOLLOWERS
--------------------------------------

CREATE TABLE directory_followers (
    user_id integer NOT NULL,
    directory_id integer NOT NULL,
    last_send timestamp without time zone,
    track_type character varying DEFAULT 'immediate'::character varying NOT NULL
);


ALTER TABLE public.directory_followers OWNER TO plugmedia;

ALTER TABLE ONLY directory_followers
    ADD CONSTRAINT df_unique UNIQUE (user_id, directory_id);

ALTER TABLE ONLY directory_followers
    ADD CONSTRAINT df_directory_id FOREIGN KEY (directory_id) REFERENCES directory(id) ON DELETE CASCADE;

ALTER TABLE ONLY directory_followers
    ADD CONSTRAINT df_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;



--------------------------------------
-- TABLE QUEUE NEWS
--------------------------------------

CREATE TABLE queue_news (
    directory_id integer NOT NULL,
    inserted_directory integer,
    inserted_file integer NOT NULL,
    updated_file integer NOT NULL,
    date timestamp without time zone
);


ALTER TABLE public.queue_news OWNER TO plugmedia;

ALTER TABLE ONLY queue_news
    ADD CONSTRAINT qn_fk_directory_id FOREIGN KEY (directory_id) REFERENCES directory(id) ON DELETE CASCADE;

-----------------------------------
-- UPDATE FOR USER TABLE
-----------------------------------
ALTER TABLE users ADD COLUMN avatar character varying(200) DEFAULT 'joker.png'::character varying NOT NULL;

-----------------------------------
-- PERFORM OPERATION
-----------------------------------
UPDATE files SET file_thumb='', file_thumb_normal = '';



-----------------------------------
-- FIX ERROR ON CONFIG
----------------------------------
ALTER TABLE "public"."sys_conf_settings" ADD CONSTRAINT "sys_conf_settings_key" UNIQUE ("conf_key");

-----------------------------------
-- UPDATE SHEMA INFO
-----------------------------------
UPDATE sys_conf_settings SET conf_default = '2.2.1' where conf_key = 'DATABASE_SCHEMA_VERSION';


-----------------------------------
-- TABLE PLUGINS
-----------------------------------
CREATE TABLE plugins (
    id integer NOT NULL,
    filename character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    version character varying(255),
    url character varying(255),
    author character varying(255),
    authorurl character varying(255),
    license character varying(255),
    description text,
    enabled smallint DEFAULT 0
);


ALTER TABLE public.plugins OWNER TO plugmedia;

CREATE SEQUENCE plugins_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER TABLE public.plugins_id_seq OWNER TO plugmedia;

ALTER SEQUENCE plugins_id_seq OWNED BY plugins.id;

ALTER TABLE plugins ALTER COLUMN id SET DEFAULT nextval('plugins_id_seq'::regclass);

ALTER TABLE ONLY plugins
    ADD CONSTRAINT plugins_filename_key UNIQUE (filename);

ALTER TABLE ONLY plugins
    ADD CONSTRAINT plugins_pkey PRIMARY KEY (id);

 
