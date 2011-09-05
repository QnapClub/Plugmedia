----------------------------------------
-- PostgreSQL database dump
----------------------------------------

SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;


COMMENT ON DATABASE plugmedia IS 'Plugmedia data repository';
COMMENT ON SCHEMA public IS 'Standard public schema';


CREATE PROCEDURAL LANGUAGE plpgsql;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;


----------------------------------------
-- Name: comments
----------------------------------------

CREATE TABLE comments (
    comment_id integer NOT NULL,
    file_id integer NOT NULL,
    user_id integer,
    displayable_name character(200) NOT NULL,
    email character(200),
    "comment" text NOT NULL,
    "time" timestamp without time zone DEFAULT now(),
    "new" smallint DEFAULT 1
);

ALTER TABLE public.comments OWNER TO plugmedia;

CREATE SEQUENCE comments_comment_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE comments_comment_id_seq OWNED BY comments.comment_id;

ALTER TABLE ONLY comments ADD CONSTRAINT comments_pkey PRIMARY KEY (comment_id);

ALTER TABLE ONLY comments
    ADD CONSTRAINT fk_comments_file_id FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE;

ALTER TABLE ONLY comments
    ADD CONSTRAINT fk_comments_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;



----------------------------------------
-- Name: directory; 
----------------------------------------

CREATE TABLE directory (
    id integer NOT NULL,
    parent text NOT NULL,
    name text,
    thumbnail text,
    thumbnail_random text,
    original_date integer,
    formated_name text
);


CREATE SEQUENCE directory_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.directory_id_seq OWNER TO plugmedia;

ALTER SEQUENCE directory_id_seq OWNED BY directory.id;

ALTER TABLE ONLY directory
    ADD CONSTRAINT directory_pkey PRIMARY KEY (id);

ALTER TABLE ONLY directory
    ADD CONSTRAINT unique_directory_p_n UNIQUE (parent, name);

CREATE INDEX formated_name ON directory USING btree (formated_name);
CREATE INDEX index_name ON directory USING btree (name);
CREATE INDEX index_parent ON directory USING btree (parent);

----------------------------------------
-- Name: files;
----------------------------------------

CREATE TABLE files (
    id integer NOT NULL,
    directory_id integer NOT NULL,
    filename text NOT NULL,
    detail_file character varying,
    timestamp_modification integer,
    original_date integer NOT NULL,
    filesize integer,
    extension character varying,
    file_thumb text,
    file_hash character varying,
    formated_name text,
    file_thumb_normal text,
    metadata_extracted smallint DEFAULT 0

);


ALTER TABLE public.files OWNER TO plugmedia;

CREATE SEQUENCE files_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.files_id_seq OWNER TO plugmedia;


ALTER SEQUENCE files_id_seq OWNED BY files.id;

ALTER TABLE ONLY files
    ADD CONSTRAINT files_pkey PRIMARY KEY (id);

ALTER TABLE ONLY files
    ADD CONSTRAINT unique_files_d_fil UNIQUE (directory_id, filename);


ALTER TABLE ONLY files
    ADD CONSTRAINT fk_files_directory FOREIGN KEY (directory_id) REFERENCES directory(id) ON DELETE CASCADE;


CREATE INDEX files_formated_name ON files USING btree (formated_name);
CREATE INDEX index_filename ON files USING btree (filename);

----------------------------------------
-- Name: group_accesspath; 
----------------------------------------

CREATE TABLE group_accesspath (
    group_id integer NOT NULL,
    directory_id integer NOT NULL
);


ALTER TABLE public.group_accesspath OWNER TO plugmedia;

ALTER TABLE ONLY group_accesspath
    ADD CONSTRAINT group_accesspath_pkey PRIMARY KEY (group_id, directory_id);

ALTER TABLE ONLY group_accesspath
    ADD CONSTRAINT fk_grpap_directory FOREIGN KEY (directory_id) REFERENCES directory(id) ON DELETE CASCADE;

ALTER TABLE ONLY group_accesspath
    ADD CONSTRAINT fk_grpap_group FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE;



----------------------------------------
-- Name: groups;
----------------------------------------

CREATE TABLE groups (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    access_path text
);


ALTER TABLE public.groups OWNER TO plugmedia;

CREATE SEQUENCE groups_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER TABLE public.groups_id_seq OWNER TO plugmedia;
ALTER SEQUENCE groups_id_seq OWNED BY groups.id;

ALTER TABLE ONLY groups
    ADD CONSTRAINT groups_name_key UNIQUE (name);
ALTER TABLE ONLY groups
    ADD CONSTRAINT groups_pkey PRIMARY KEY (id);


----------------------------------------
-- Name: metadata_exif; 
----------------------------------------

CREATE TABLE metadata_exif (
    files_id integer NOT NULL,
    orientation character varying(255),
    ycbcrpositioning character varying(255),
    xresolution character varying(255),
    yresolution character varying(255),
    resolutionunit character varying(255),
    datetime character varying(255),
    imagedescription character varying(255),
    make character varying(255),
    model character varying(255),
    software character varying(255),
    artist character varying(255),
    copyright character varying(255),
    colorspace character varying(255),
    componentsconfiguration character varying(255),
    compressedbitsperpixel character varying(255),
    pixelxdimension character varying(255),
    pixelydimension character varying(255),
    usercomment character varying(255),
    datetimeoriginal character varying(255),
    datetimedigitized character varying(255),
    exposuretime character varying(255),
    fnumber character varying(255),
    exposureprogram character varying(255),
    isospeedratings character varying(255),
    shutterspeedvalue character varying(255),
    aperturevalue character varying(255),
    brightnessvalue character varying(255),
    exposurebiasvalue character varying(255),
    maxaperturevalue character varying(255),
    subjectdistance character varying(255),
    meteringmode character varying(255),
    lightsource character varying(255),
    flash character varying(255),
    focallength character varying(255),
    focalplanexresolution character varying(255),
    focalplaneyresolution character varying(255),
    focalplaneresolutionunit character varying(255),
    sensingmethod character varying(255),
    filesource character varying(255),
    scenetype character varying(255),
    customrendered character varying(255),
    exposuremode character varying(255),
    whitebalance character varying(255),
    digitalzoomratio character varying(255),
    scenecapturetype character varying(255),
    gaincontrol character varying(255),
    contrast character varying(255),
    saturation character varying(255),
    sharpness character varying(255),
    imageuniqueid character varying(255),
    jpgcomment character varying(255)
);


ALTER TABLE public.metadata_exif OWNER TO plugmedia;

ALTER TABLE ONLY metadata_exif
    ADD CONSTRAINT metadata_exif_pkey PRIMARY KEY (files_id);

ALTER TABLE ONLY metadata_exif
    ADD CONSTRAINT fk_metadata_file_id FOREIGN KEY (files_id) REFERENCES files(id) ON DELETE CASCADE;


----------------------------------------
-- Name: mimetype;
----------------------------------------

CREATE TABLE mimetype (
    id integer NOT NULL,
    extension character varying(18) NOT NULL,
    mimetype character varying(255) NOT NULL
);


ALTER TABLE public.mimetype OWNER TO plugmedia;

----------------------------------------
-- Name: radio_listener;
----------------------------------------

CREATE TABLE radio_listener (
    id_radio integer NOT NULL,
    id_listener integer,
    song text,
    last_access_date timestamp without time zone DEFAULT now()
);


ALTER TABLE public.radio_listener OWNER TO plugmedia;

CREATE SEQUENCE radio_listener_id_radio_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.radio_listener_id_radio_seq OWNER TO plugmedia;
ALTER SEQUENCE radio_listener_id_radio_seq OWNED BY radio_listener.id_radio;


ALTER TABLE ONLY radio_listener
    ADD CONSTRAINT radio_listener_ppk PRIMARY KEY (id_radio);


----------------------------------------
-- Name: radio_token; 
----------------------------------------

CREATE TABLE radio_token (
    token character varying(20) NOT NULL,
    id_creator integer NOT NULL,
    id_directory integer,
    create_date timestamp without time zone DEFAULT now(),
    last_access_date timestamp without time zone DEFAULT now()
);


ALTER TABLE public.radio_token OWNER TO plugmedia;


----------------------------------------
-- Name: sys_conf_settings;
----------------------------------------

CREATE TABLE sys_conf_settings (
    conf_id integer NOT NULL,
    conf_group smallint DEFAULT 0 NOT NULL,
    conf_key character varying NOT NULL,
    conf_value text,
    conf_default text,
    "enable" smallint DEFAULT 1
);


ALTER TABLE public.sys_conf_settings OWNER TO plugmedia;

CREATE SEQUENCE sys_conf_settings_conf_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.sys_conf_settings_conf_id_seq OWNER TO plugmedia;

ALTER SEQUENCE sys_conf_settings_conf_id_seq OWNED BY sys_conf_settings.conf_id;

ALTER TABLE ONLY sys_conf_settings ADD CONSTRAINT sys_conf_unique UNIQUE (conf_key);


----------------------------------------
-- Name: users;
----------------------------------------

CREATE TABLE users (
    id integer NOT NULL,
    "login" character varying(100) NOT NULL,
    name character varying(100) NOT NULL,
    "password" character varying(100) NOT NULL,
    salt character varying(10) NOT NULL,
    email character varying(100),
    lang character varying(3) DEFAULT 'en'::character varying NOT NULL,
    can_read_comment smallint DEFAULT 0,
    can_add_comment smallint DEFAULT 0,
    default_view character varying(100) DEFAULT 'thumb'::character varying NOT NULL,
    creation_date timestamp without time zone DEFAULT now(),
    last_access timestamp without time zone DEFAULT now(),
    last_ip character varying(100),
    embeded smallint DEFAULT 0,
    admin_access smallint DEFAULT 0
);


ALTER TABLE public.users OWNER TO plugmedia;

CREATE SEQUENCE users_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO plugmedia;

ALTER SEQUENCE users_id_seq OWNED BY users.id;

ALTER TABLE ONLY users
    ADD CONSTRAINT users_login_key UNIQUE ("login");

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);



----------------------------------------
-- Name: usr_grp_mapping; 
----------------------------------------

CREATE TABLE usr_grp_mapping (
    id_grp integer NOT NULL,
    id_usr integer NOT NULL
);


ALTER TABLE public.usr_grp_mapping OWNER TO plugmedia;

ALTER TABLE ONLY usr_grp_mapping
    ADD CONSTRAINT usr_grp_mapping_id_grp FOREIGN KEY (id_grp) REFERENCES groups(id) ON DELETE CASCADE;


ALTER TABLE ONLY usr_grp_mapping
    ADD CONSTRAINT usr_grp_mapping_pkey PRIMARY KEY (id_grp, id_usr);

----------------------------------------
-- Name: usr_grp_mapping; 
----------------------------------------


CREATE TABLE metadata_id3 (
    files_id integer,
    track character varying,
    title character varying,
    album character varying,
    gender character varying,
    "year" character varying,
    artist character varying
);


ALTER TABLE public.metadata_id3 OWNER TO plugmedia;

ALTER TABLE ONLY metadata_id3
    ADD CONSTRAINT mtd_id3 FOREIGN KEY (files_id) REFERENCES files(id) ON DELETE CASCADE;






ALTER TABLE comments ALTER COLUMN comment_id SET DEFAULT nextval('comments_comment_id_seq'::regclass);
ALTER TABLE directory ALTER COLUMN id SET DEFAULT nextval('directory_id_seq'::regclass);
ALTER TABLE files ALTER COLUMN id SET DEFAULT nextval('files_id_seq'::regclass);
ALTER TABLE groups ALTER COLUMN id SET DEFAULT nextval('groups_id_seq'::regclass);
ALTER TABLE radio_listener ALTER COLUMN id_radio SET DEFAULT nextval('radio_listener_id_radio_seq'::regclass);
ALTER TABLE sys_conf_settings ALTER COLUMN conf_id SET DEFAULT nextval('sys_conf_settings_conf_id_seq'::regclass);
ALTER TABLE users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);

