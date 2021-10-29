--
-- PostgreSQL database dump
--

-- Dumped from database version 13.2 (Ubuntu 13.2-1.pgdg20.04+1)
-- Dumped by pg_dump version 13.2 (Ubuntu 13.2-1.pgdg20.04+1)

-- Started on 2021-05-18 10:01:33 EDT
CREATE SCHEMA IF NOT EXISTS bayarea;

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 207 (class 1259 OID 16557)
-- Name: uploaded_datasets; Type: TABLE; Schema: bayarea; Owner: choroplethmaps
--

CREATE TABLE bayarea.uploaded_datasets (
    dataset_name character varying,
    lat double precision,
    lon double precision,
    zip_code integer,
    id integer NOT NULL
);


ALTER TABLE bayarea.uploaded_datasets OWNER TO choroplethmaps;

--
-- TOC entry 206 (class 1259 OID 16555)
-- Name: uploaded_datasets_id_seq; Type: SEQUENCE; Schema: bayarea; Owner: choroplethmaps
--

ALTER TABLE bayarea.uploaded_datasets ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME bayarea.uploaded_datasets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);

--
-- TOC entry 3027 (class 0 OID 0)
-- Dependencies: 206
-- Name: uploaded_datasets_id_seq; Type: SEQUENCE SET; Schema: bayarea; Owner: choroplethmaps
--

SELECT pg_catalog.setval('bayarea.uploaded_datasets_id_seq', 97329, true);


--
-- TOC entry 2888 (class 2606 OID 16564)
-- Name: uploaded_datasets uploaded_datasets_pkey; Type: CONSTRAINT; Schema: bayarea; Owner: choroplethmaps
--

ALTER TABLE ONLY bayarea.uploaded_datasets
    ADD CONSTRAINT uploaded_datasets_pkey PRIMARY KEY (id);


--
-- TOC entry 2886 (class 1259 OID 16565)
-- Name: query_index; Type: INDEX; Schema: bayarea; Owner: choroplethmaps
--

CREATE INDEX query_index ON bayarea.uploaded_datasets USING btree (dataset_name, zip_code, lat, lon);


--
-- TOC entry 3026 (class 0 OID 0)
-- Dependencies: 207
-- Name: TABLE uploaded_datasets; Type: ACL; Schema: bayarea; Owner: choroplethmaps
--

GRANT ALL ON TABLE bayarea.uploaded_datasets TO choroplethmaps;


-- Completed on 2021-05-18 10:01:33 EDT

--
-- PostgreSQL database dump complete
--

