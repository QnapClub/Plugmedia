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

------------------------------------
-- ADD COLUMN TO FILES
------------------------------------
ALTER TABLE users ADD COLUMN can_convert_movie smallint default 0;
UPDATE users SET can_convert_movie = 1 WHERE login='admin';