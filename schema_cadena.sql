-- SISTEMA DE GESTION DE RESTAURANTES - ESQUEMA CADENA
-- Script depurado del export de pgAdmin 4.
-- Conserva las 38 tablas del esquema cadena y sus 45 claves foraneas.
-- No modifica ni elimina objetos del esquema public.
-- Ejecutar en la base de datos correcta desde pgAdmin 4.
-- Para una instalacion inicial en una base vacia.

BEGIN;
CREATE SCHEMA IF NOT EXISTS cadena;
SET LOCAL search_path TO cadena, public;

CREATE TABLE IF NOT EXISTS cadena.alergia
(
    id smallint NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 32767 CACHE 1 ),
    nombre character varying(80) COLLATE pg_catalog."default" NOT NULL,
    descripcion text COLLATE pg_catalog."default",
    CONSTRAINT alergia_pkey PRIMARY KEY (id),
    CONSTRAINT alergia_nombre_key UNIQUE (nombre)
);

CREATE TABLE IF NOT EXISTS cadena.beneficio
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    nombre character varying(100) COLLATE pg_catalog."default" NOT NULL,
    descripcion character varying(250) COLLATE pg_catalog."default" NOT NULL,
    vigencia_ini date,
    vigencia_fin date,
    CONSTRAINT beneficio_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS cadena.bitacora
(
    id bigint NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1 ),
    usuario_id integer,
    accion character varying(10) COLLATE pg_catalog."default" NOT NULL,
    registro_id text COLLATE pg_catalog."default",
    fecha_hora timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT bitacora_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS cadena.categoria_platillo
(
    id smallint NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 32767 CACHE 1 ),
    nombre character varying(60) COLLATE pg_catalog."default" NOT NULL,
    descripcion text COLLATE pg_catalog."default",
    CONSTRAINT categoria_platillo_pkey PRIMARY KEY (id),
    CONSTRAINT categoria_platillo_nombre_key UNIQUE (nombre)
);

CREATE TABLE IF NOT EXISTS cadena.cliente
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    nombres character varying(150) COLLATE pg_catalog."default" NOT NULL,
    apellidos character varying(150) COLLATE pg_catalog."default" NOT NULL,
    telefono character varying(20) COLLATE pg_catalog."default",
    correo character varying(150) COLLATE pg_catalog."default",
    fecha_nacimiento date,
    fecha_alta timestamp with time zone NOT NULL DEFAULT now(),
    sucursal_alta_id integer,
    nivel_lealtad_id smallint,
    puntos_saldo integer NOT NULL DEFAULT 0,
    puntos_acumulados bigint NOT NULL DEFAULT 0,
    tratamiento_bloqueado boolean NOT NULL DEFAULT false,
    activo boolean NOT NULL DEFAULT true,
    updated_at timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT cliente_pkey PRIMARY KEY (id),
    CONSTRAINT cliente_correo_key UNIQUE (correo)
);

CREATE TABLE IF NOT EXISTS cadena.cliente_alergia
(
    cliente_id integer NOT NULL,
    alergia_id smallint NOT NULL,
    severidad_id smallint NOT NULL,
    notas text COLLATE pg_catalog."default",
    CONSTRAINT cliente_alergia_pkey PRIMARY KEY (cliente_id, alergia_id)
);

CREATE TABLE IF NOT EXISTS cadena.cliente_preferencia
(
    cliente_id integer NOT NULL,
    preferencia_id smallint NOT NULL,
    notas text COLLATE pg_catalog."default",
    CONSTRAINT cliente_preferencia_pkey PRIMARY KEY (cliente_id, preferencia_id)
);

CREATE TABLE IF NOT EXISTS cadena.consentimiento
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    cliente_id integer NOT NULL,
    tipo character varying(40) COLLATE pg_catalog."default" NOT NULL,
    aceptado boolean NOT NULL,
    fecha timestamp with time zone NOT NULL DEFAULT now(),
    version_aviso character varying(20) COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT consentimiento_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS cadena.empleado
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    nombres character varying(150) COLLATE pg_catalog."default" NOT NULL,
    apellidos character varying(150) COLLATE pg_catalog."default" NOT NULL,
    telefono character varying(20) COLLATE pg_catalog."default",
    fecha_ingreso date NOT NULL DEFAULT CURRENT_DATE,
    estatus character varying(30) COLLATE pg_catalog."default" NOT NULL DEFAULT 'activo'::character varying,
    puesto character varying(60) COLLATE pg_catalog."default" NOT NULL,
    created_at timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT empleado_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS cadena.empleado_sucursal
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    empleado_id integer NOT NULL,
    sucursal_id integer NOT NULL,
    CONSTRAINT empleado_sucursal_pkey PRIMARY KEY (id),
    CONSTRAINT empleado_sucursal_emp_suc_key UNIQUE (empleado_id, sucursal_id)
);

CREATE TABLE IF NOT EXISTS cadena.fecha_especial
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    cliente_id integer NOT NULL,
    tipo character varying(30) COLLATE pg_catalog."default" NOT NULL,
    fecha date NOT NULL,
    descripcion text COLLATE pg_catalog."default",
    CONSTRAINT fecha_especial_pkey PRIMARY KEY (id),
    CONSTRAINT fecha_especial_cliente_id_tipo_fecha_key UNIQUE (cliente_id, tipo, fecha)
);

CREATE TABLE IF NOT EXISTS cadena.ingrediente
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    nombre character varying(100) COLLATE pg_catalog."default" NOT NULL,
    descripcion text COLLATE pg_catalog."default",
    CONSTRAINT ingrediente_pkey PRIMARY KEY (id),
    CONSTRAINT ingrediente_nombre_key UNIQUE (nombre)
);

CREATE TABLE IF NOT EXISTS cadena.mesa
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    codigo_mesa character varying(10) COLLATE pg_catalog."default" NOT NULL,
    capacidad smallint NOT NULL DEFAULT 4,
    activa boolean NOT NULL DEFAULT true,
    CONSTRAINT mesa_pkey PRIMARY KEY (id),
    CONSTRAINT mesa_codigo_key UNIQUE (codigo_mesa)
);

CREATE TABLE IF NOT EXISTS cadena.mesa_sucursal
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    sucursal_id integer NOT NULL,
    mesa_id integer NOT NULL,
    CONSTRAINT mesa_sucursal_pkey PRIMARY KEY (id),
    CONSTRAINT mesa_sucursal_suc_mesa_key UNIQUE (sucursal_id, mesa_id)
);

CREATE TABLE IF NOT EXISTS cadena.movimiento_puntos
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    cliente_id integer NOT NULL,
    nota_reservacion_id integer,
    regla_id integer,
    puntos integer NOT NULL,
    tipo character varying(30) COLLATE pg_catalog."default" NOT NULL,
    fecha timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT movimiento_puntos_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS cadena.nivel_beneficio
(
    nivel_lealtad_id smallint NOT NULL,
    beneficio_id integer NOT NULL,
    CONSTRAINT nivel_beneficio_pkey PRIMARY KEY (nivel_lealtad_id, beneficio_id)
);

CREATE TABLE IF NOT EXISTS cadena.nivel_lealtad
(
    id smallint NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 32767 CACHE 1 ),
    nombre character varying(50) COLLATE pg_catalog."default" NOT NULL,
    puntos_minimos integer NOT NULL,
    CONSTRAINT nivel_lealtad_pkey PRIMARY KEY (id),
    CONSTRAINT nivel_lealtad_nombre_key UNIQUE (nombre),
    CONSTRAINT nivel_lealtad_puntos_minimos_key UNIQUE (puntos_minimos)
);

CREATE TABLE IF NOT EXISTS cadena.nota_reservacion
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    sucursal_id integer NOT NULL,
    reservacion_id integer,
    empleado_id integer,
    subtotal numeric(12, 2) NOT NULL DEFAULT 0,
    descuento numeric(12, 2) NOT NULL DEFAULT 0,
    impuestos numeric(12, 2) NOT NULL DEFAULT 0,
    propina numeric(12, 2) NOT NULL DEFAULT 0,
    total numeric(12, 2) NOT NULL DEFAULT 0,
    calificacion smallint,
    comentarios text COLLATE pg_catalog."default",
    fecha_emision timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT nota_reservacion_pkey PRIMARY KEY (id),
    CONSTRAINT nota_reservacion_reservacion_key UNIQUE (reservacion_id)
);

CREATE TABLE IF NOT EXISTS cadena.pedido
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    nota_reservacion_id integer NOT NULL,
    estatus character varying(15) COLLATE pg_catalog."default" NOT NULL DEFAULT 'abierto'::character varying,
    fecha_hora timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT pedido_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS cadena.pedido_detalle
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    pedido_id integer NOT NULL,
    platillo_id integer NOT NULL,
    cantidad smallint NOT NULL,
    cantidad_cancelada smallint NOT NULL DEFAULT 0,
    estatus character varying(15) COLLATE pg_catalog."default" GENERATED ALWAYS AS (
CASE
    WHEN (cantidad_cancelada = 0) THEN 'activo'::text
    WHEN (cantidad_cancelada = cantidad) THEN 'cancelado'::text
    ELSE 'parcial'::text
END) STORED,
    precio_unitario numeric(10, 2) NOT NULL,
    es_cortesia boolean NOT NULL DEFAULT false,
    notas text COLLATE pg_catalog."default",
    CONSTRAINT pedido_detalle_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS cadena.pedido_detalle_ajuste
(
    id bigint NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1 ),
    pedido_detalle_id integer NOT NULL,
    tipo character varying(15) COLLATE pg_catalog."default" NOT NULL,
    cantidad smallint NOT NULL,
    motivo character varying(250) COLLATE pg_catalog."default" NOT NULL,
    descripcion text COLLATE pg_catalog."default",
    destino character varying(15) COLLATE pg_catalog."default" NOT NULL,
    usuario_id integer,
    fecha_hora timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT pedido_detalle_ajuste_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS cadena.permiso
(
    id smallint NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 32767 CACHE 1 ),
    codigo character varying(80) COLLATE pg_catalog."default" NOT NULL,
    descripcion character varying(200) COLLATE pg_catalog."default",
    CONSTRAINT permiso_pkey PRIMARY KEY (id),
    CONSTRAINT permiso_codigo_key UNIQUE (codigo)
);

CREATE TABLE IF NOT EXISTS cadena.platillo
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    categoria_id smallint NOT NULL,
    temporada_id integer,
    nombre character varying(120) COLLATE pg_catalog."default" NOT NULL,
    descripcion text COLLATE pg_catalog."default",
    precio numeric(10, 2) NOT NULL,
    imagen_url character varying(500) COLLATE pg_catalog."default",
    estatus character varying(20) COLLATE pg_catalog."default" NOT NULL DEFAULT 'activo'::character varying,
    disponible boolean NOT NULL DEFAULT true,
    created_at timestamp with time zone NOT NULL DEFAULT now(),
    updated_at timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT platillo_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS cadena.platillo_alergeno
(
    platillo_id integer NOT NULL,
    alergia_id smallint NOT NULL,
    CONSTRAINT platillo_alergeno_pkey PRIMARY KEY (platillo_id, alergia_id)
);

CREATE TABLE IF NOT EXISTS cadena.platillo_ingrediente
(
    platillo_id integer NOT NULL,
    ingrediente_id integer NOT NULL,
    CONSTRAINT platillo_ingrediente_pkey PRIMARY KEY (platillo_id, ingrediente_id)
);

CREATE TABLE IF NOT EXISTS cadena.platillo_sucursal
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    sucursal_id integer NOT NULL,
    platillo_id integer NOT NULL,
    precio_local numeric(10, 2),
    disponible boolean NOT NULL DEFAULT true,
    CONSTRAINT platillo_sucursal_pkey PRIMARY KEY (id),
    CONSTRAINT platillo_sucursal_unica_key UNIQUE (sucursal_id, platillo_id)
);

CREATE TABLE IF NOT EXISTS cadena.preferencia
(
    id smallint NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 32767 CACHE 1 ),
    tipo character varying(20) COLLATE pg_catalog."default" NOT NULL,
    descripcion character varying(150) COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT preferencia_pkey PRIMARY KEY (id),
    CONSTRAINT preferencia_tipo_descripcion_key UNIQUE (tipo, descripcion)
);

CREATE TABLE IF NOT EXISTS cadena.regla_puntos
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    monto_por_punto numeric(12, 2) NOT NULL,
    vigencia_inicio date NOT NULL,
    vigencia_fin date,
    activa boolean NOT NULL DEFAULT true,
    CONSTRAINT regla_puntos_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS cadena.reservacion
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    cliente_id integer NOT NULL,
    fecha_hora_inicio timestamp with time zone NOT NULL,
    fecha_hora_final timestamp with time zone NOT NULL,
    cantidad_personas smallint NOT NULL,
    estatus character varying(15) COLLATE pg_catalog."default" NOT NULL DEFAULT 'pendiente'::character varying,
    notas text COLLATE pg_catalog."default",
    created_at timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT reservacion_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS cadena.reservacion_mesa
(
    reservacion_id integer NOT NULL,
    mesa_sucursal_id integer NOT NULL,
    periodo tstzrange NOT NULL,
    bloquea boolean NOT NULL,
    CONSTRAINT reservacion_mesa_pkey PRIMARY KEY (reservacion_id, mesa_sucursal_id)
);

CREATE TABLE IF NOT EXISTS cadena.rol
(
    id smallint NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 32767 CACHE 1 ),
    nombre character varying(50) COLLATE pg_catalog."default" NOT NULL,
    descripcion character varying(150) COLLATE pg_catalog."default",
    CONSTRAINT rol_pkey PRIMARY KEY (id),
    CONSTRAINT rol_nombre_key UNIQUE (nombre)
);

CREATE TABLE IF NOT EXISTS cadena.rol_permiso
(
    rol_id smallint NOT NULL,
    permiso_id smallint NOT NULL,
    CONSTRAINT rol_permiso_pkey PRIMARY KEY (rol_id, permiso_id)
);

CREATE TABLE IF NOT EXISTS cadena.severidad_alergia
(
    id smallint NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 32767 CACHE 1 ),
    nombre character varying(40) COLLATE pg_catalog."default" NOT NULL,
    prioridad smallint NOT NULL,
    CONSTRAINT severidad_alergia_pkey PRIMARY KEY (id),
    CONSTRAINT severidad_alergia_nombre_key UNIQUE (nombre),
    CONSTRAINT severidad_alergia_prioridad_key UNIQUE (prioridad)
);

CREATE TABLE IF NOT EXISTS cadena.solicitud_arco
(
    id bigint NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1 ),
    cliente_id integer NOT NULL,
    tipo character varying(15) COLLATE pg_catalog."default" NOT NULL,
    estatus character varying(20) COLLATE pg_catalog."default" NOT NULL DEFAULT 'recibida'::character varying,
    fecha_solicitud timestamp with time zone NOT NULL DEFAULT now(),
    fecha_resolucion timestamp with time zone,
    notas text COLLATE pg_catalog."default",
    CONSTRAINT solicitud_arco_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS cadena.solicitud_arco_historial
(
    id bigint NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1 ),
    solicitud_id bigint NOT NULL,
    usuario_id integer,
    estatus_anterior character varying(20) COLLATE pg_catalog."default",
    estatus_nuevo character varying(20) COLLATE pg_catalog."default" NOT NULL,
    fecha timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT solicitud_arco_historial_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS cadena.sucursal
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    nombre character varying(100) COLLATE pg_catalog."default" NOT NULL,
    direccion character varying(250) COLLATE pg_catalog."default",
    telefono character varying(20) COLLATE pg_catalog."default",
    activa boolean NOT NULL DEFAULT true,
    created_at timestamp with time zone NOT NULL DEFAULT now(),
    updated_at timestamp with time zone NOT NULL DEFAULT now(),
    CONSTRAINT sucursal_pkey PRIMARY KEY (id),
    CONSTRAINT sucursal_nombre_key UNIQUE (nombre)
);

CREATE TABLE IF NOT EXISTS cadena.temporada
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    nombre character varying(80) COLLATE pg_catalog."default" NOT NULL,
    fecha_inicio date NOT NULL,
    fecha_final date NOT NULL,
    CONSTRAINT temporada_pkey PRIMARY KEY (id),
    CONSTRAINT temporada_nombre_key UNIQUE (nombre)
);

CREATE TABLE IF NOT EXISTS cadena.usuario
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    empleado_id integer NOT NULL,
    correo character varying(150) COLLATE pg_catalog."default" NOT NULL,
    hash_password text COLLATE pg_catalog."default" NOT NULL,
    rol_id smallint NOT NULL,
    activo boolean NOT NULL DEFAULT true,
    ultimo_acceso timestamp with time zone,
    CONSTRAINT usuario_pkey PRIMARY KEY (id),
    CONSTRAINT usuario_correo_key UNIQUE (correo),
    CONSTRAINT usuario_empleado_id_key UNIQUE (empleado_id)
);

-- RELACIONES / CLAVES FORANEAS

ALTER TABLE IF EXISTS cadena.bitacora
    ADD CONSTRAINT bitacora_usuario_id_fkey FOREIGN KEY (usuario_id)
    REFERENCES cadena.usuario (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.cliente
    ADD CONSTRAINT cliente_nivel_lealtad_id_fkey FOREIGN KEY (nivel_lealtad_id)
    REFERENCES cadena.nivel_lealtad (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.cliente
    ADD CONSTRAINT cliente_sucursal_alta_id_fkey FOREIGN KEY (sucursal_alta_id)
    REFERENCES cadena.sucursal (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.cliente_alergia
    ADD CONSTRAINT cliente_alergia_alergia_id_fkey FOREIGN KEY (alergia_id)
    REFERENCES cadena.alergia (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.cliente_alergia
    ADD CONSTRAINT cliente_alergia_cliente_id_fkey FOREIGN KEY (cliente_id)
    REFERENCES cadena.cliente (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE CASCADE;


ALTER TABLE IF EXISTS cadena.cliente_alergia
    ADD CONSTRAINT cliente_alergia_severidad_id_fkey FOREIGN KEY (severidad_id)
    REFERENCES cadena.severidad_alergia (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.cliente_preferencia
    ADD CONSTRAINT cliente_preferencia_cliente_id_fkey FOREIGN KEY (cliente_id)
    REFERENCES cadena.cliente (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE CASCADE;


ALTER TABLE IF EXISTS cadena.cliente_preferencia
    ADD CONSTRAINT cliente_preferencia_preferencia_id_fkey FOREIGN KEY (preferencia_id)
    REFERENCES cadena.preferencia (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.consentimiento
    ADD CONSTRAINT consentimiento_cliente_id_fkey FOREIGN KEY (cliente_id)
    REFERENCES cadena.cliente (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE CASCADE;


ALTER TABLE IF EXISTS cadena.empleado_sucursal
    ADD CONSTRAINT emp_suc_empleado_fkey FOREIGN KEY (empleado_id)
    REFERENCES cadena.empleado (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE CASCADE;


ALTER TABLE IF EXISTS cadena.empleado_sucursal
    ADD CONSTRAINT emp_suc_sucursal_fkey FOREIGN KEY (sucursal_id)
    REFERENCES cadena.sucursal (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE CASCADE;


ALTER TABLE IF EXISTS cadena.fecha_especial
    ADD CONSTRAINT fecha_especial_cliente_id_fkey FOREIGN KEY (cliente_id)
    REFERENCES cadena.cliente (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE CASCADE;


ALTER TABLE IF EXISTS cadena.mesa_sucursal
    ADD CONSTRAINT mesa_sucursal_mesa_fkey FOREIGN KEY (mesa_id)
    REFERENCES cadena.mesa (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE CASCADE;


ALTER TABLE IF EXISTS cadena.mesa_sucursal
    ADD CONSTRAINT mesa_sucursal_sucursal_fkey FOREIGN KEY (sucursal_id)
    REFERENCES cadena.sucursal (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE CASCADE;


ALTER TABLE IF EXISTS cadena.movimiento_puntos
    ADD CONSTRAINT movimiento_puntos_cliente_id_fkey FOREIGN KEY (cliente_id)
    REFERENCES cadena.cliente (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.movimiento_puntos
    ADD CONSTRAINT movimiento_puntos_nota_res_fkey FOREIGN KEY (nota_reservacion_id)
    REFERENCES cadena.nota_reservacion (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.movimiento_puntos
    ADD CONSTRAINT movimiento_puntos_regla_id_fkey FOREIGN KEY (regla_id)
    REFERENCES cadena.regla_puntos (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.nivel_beneficio
    ADD CONSTRAINT nivel_ben_beneficio_fkey FOREIGN KEY (beneficio_id)
    REFERENCES cadena.beneficio (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE CASCADE;


ALTER TABLE IF EXISTS cadena.nivel_beneficio
    ADD CONSTRAINT nivel_ben_lealtad_fkey FOREIGN KEY (nivel_lealtad_id)
    REFERENCES cadena.nivel_lealtad (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE CASCADE;


ALTER TABLE IF EXISTS cadena.nota_reservacion
    ADD CONSTRAINT nota_res_empleado_fkey FOREIGN KEY (empleado_id)
    REFERENCES cadena.empleado (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.nota_reservacion
    ADD CONSTRAINT nota_res_reservacion_fkey FOREIGN KEY (reservacion_id)
    REFERENCES cadena.reservacion (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;
ALTER TABLE IF EXISTS cadena.nota_reservacion
    ADD CONSTRAINT nota_res_sucursal_fkey FOREIGN KEY (sucursal_id)
    REFERENCES cadena.sucursal (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.pedido
    ADD CONSTRAINT pedido_nota_res_fkey FOREIGN KEY (nota_reservacion_id)
    REFERENCES cadena.nota_reservacion (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE RESTRICT;


ALTER TABLE IF EXISTS cadena.pedido_detalle
    ADD CONSTRAINT pedido_detalle_pedido_id_fkey FOREIGN KEY (pedido_id)
    REFERENCES cadena.pedido (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE RESTRICT;


ALTER TABLE IF EXISTS cadena.pedido_detalle
    ADD CONSTRAINT pedido_detalle_platillo_id_fkey FOREIGN KEY (platillo_id)
    REFERENCES cadena.platillo (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.pedido_detalle_ajuste
    ADD CONSTRAINT pedido_detalle_ajuste_pedido_detalle_id_fkey FOREIGN KEY (pedido_detalle_id)
    REFERENCES cadena.pedido_detalle (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE RESTRICT;


ALTER TABLE IF EXISTS cadena.pedido_detalle_ajuste
    ADD CONSTRAINT pedido_detalle_ajuste_usuario_id_fkey FOREIGN KEY (usuario_id)
    REFERENCES cadena.usuario (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.platillo
    ADD CONSTRAINT platillo_categoria_id_fkey FOREIGN KEY (categoria_id)
    REFERENCES cadena.categoria_platillo (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.platillo
    ADD CONSTRAINT platillo_temporada_id_fkey FOREIGN KEY (temporada_id)
    REFERENCES cadena.temporada (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE SET NULL;


ALTER TABLE IF EXISTS cadena.platillo_alergeno
    ADD CONSTRAINT platillo_alergeno_alergia_id_fkey FOREIGN KEY (alergia_id)
    REFERENCES cadena.alergia (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.platillo_alergeno
    ADD CONSTRAINT platillo_alergeno_platillo_id_fkey FOREIGN KEY (platillo_id)
    REFERENCES cadena.platillo (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE CASCADE;


ALTER TABLE IF EXISTS cadena.platillo_ingrediente
    ADD CONSTRAINT platillo_ingrediente_ingrediente_id_fkey FOREIGN KEY (ingrediente_id)
    REFERENCES cadena.ingrediente (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.platillo_ingrediente
    ADD CONSTRAINT platillo_ingrediente_platillo_id_fkey FOREIGN KEY (platillo_id)
    REFERENCES cadena.platillo (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE CASCADE;


ALTER TABLE IF EXISTS cadena.platillo_sucursal
    ADD CONSTRAINT platillo_suc_platillo_fkey FOREIGN KEY (platillo_id)
    REFERENCES cadena.platillo (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE CASCADE;


ALTER TABLE IF EXISTS cadena.platillo_sucursal
    ADD CONSTRAINT platillo_suc_sucursal_fkey FOREIGN KEY (sucursal_id)
    REFERENCES cadena.sucursal (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE CASCADE;


ALTER TABLE IF EXISTS cadena.reservacion
    ADD CONSTRAINT reservacion_cliente_id_fkey FOREIGN KEY (cliente_id)
    REFERENCES cadena.cliente (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.reservacion_mesa
    ADD CONSTRAINT res_mesa_mesa_suc_fkey FOREIGN KEY (mesa_sucursal_id)
    REFERENCES cadena.mesa_sucursal (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.reservacion_mesa
    ADD CONSTRAINT res_mesa_reservacion_fkey FOREIGN KEY (reservacion_id)
    REFERENCES cadena.reservacion (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE CASCADE;


ALTER TABLE IF EXISTS cadena.rol_permiso
    ADD CONSTRAINT rol_permiso_permiso_id_fkey FOREIGN KEY (permiso_id)
    REFERENCES cadena.permiso (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE CASCADE;


ALTER TABLE IF EXISTS cadena.rol_permiso
    ADD CONSTRAINT rol_permiso_rol_id_fkey FOREIGN KEY (rol_id)
    REFERENCES cadena.rol (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE CASCADE;


ALTER TABLE IF EXISTS cadena.solicitud_arco
    ADD CONSTRAINT solicitud_arco_cliente_id_fkey FOREIGN KEY (cliente_id)
    REFERENCES cadena.cliente (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.solicitud_arco_historial
    ADD CONSTRAINT solicitud_arco_historial_solicitud_id_fkey FOREIGN KEY (solicitud_id)
    REFERENCES cadena.solicitud_arco (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.solicitud_arco_historial
    ADD CONSTRAINT solicitud_arco_historial_usuario_id_fkey FOREIGN KEY (usuario_id)
    REFERENCES cadena.usuario (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;


ALTER TABLE IF EXISTS cadena.usuario
    ADD CONSTRAINT usuario_empleado_id_fkey FOREIGN KEY (empleado_id)
    REFERENCES cadena.empleado (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;
ALTER TABLE IF EXISTS cadena.usuario
    ADD CONSTRAINT usuario_rol_id_fkey FOREIGN KEY (rol_id)
    REFERENCES cadena.rol (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;

COMMIT;
