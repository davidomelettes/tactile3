BEGIN;

-- Enables UUID data type
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Allows use of complex cryptographic hash algorithms like SHA256
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- Creates sha256() hashing function
CREATE OR REPLACE FUNCTION sha256(text) returns text AS $$
	SELECT encode(digest($1, 'sha256'), 'hex')
	$$ LANGUAGE SQL STRICT IMMUTABLE;

-- Create log table
CREATE TABLE log (
	level INT NOT NULL DEFAULT '7',
	created TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
	tag VARCHAR NOT NULL,
	message TEXT
);

-- Create acl and user tables so console users have an authentication identity to work with
CREATE TABLE acl_roles (
	role VARCHAR PRIMARY KEY,
	label VARCHAR NOT NULL
);
INSERT INTO acl_roles (role, label) VALUES ('guest', 'Guest');
INSERT INTO acl_roles (role, label) VALUES ('user', 'User');
INSERT INTO acl_roles (role, label) VALUES ('admin', 'Admin');
INSERT INTO acl_roles (role, label) VALUES ('super', 'Superuser');
INSERT INTO acl_roles (role, label) VALUES ('system', 'System');
CREATE TABLE users (
	key UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
	name VARCHAR NOT NULL UNIQUE,
	created_by UUID NOT NULL REFERENCES users(key),
	updated_by UUID NOT NULL REFERENCES users(key),
	created TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
	updated TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
	deleted TIMESTAMP WITH TIME ZONE,
	full_name VARCHAR NOT NULL,
	salt VARCHAR NOT NULL DEFAULT uuid_generate_v4(),
	password_hash VARCHAR NOT NULL,
	acl_role VARCHAR NOT NULL REFERENCES acl_roles(role),
	enabled BOOLEAN NOT NULL DEFAULT true,
	name_reset_name VARCHAR,
	name_reset_key UUID,
	name_reset_requested TIMESTAMP WITH TIME ZONE,
	password_reset_key UUID,
	password_reset_requested TIMESTAMP WITH TIME ZONE
);
INSERT INTO users (key, name, created_by, updated_by, full_name, password_hash, acl_role) VALUES (
	'deadbeef7a6940e789848d3de3bedc0b',
	'SYSTEM_SYSTEM',
	'deadbeef7a6940e789848d3de3bedc0b',
	'deadbeef7a6940e789848d3de3bedc0b',
	'System Account',
	'SYSTEM_SYSTEM',
	'system'
);
INSERT INTO users (key, name, created_by, updated_by, full_name, password_hash, acl_role) VALUES (
	'bedabb1e66ff47f0a3f01f3f45b5c94d',
	'SYSTEM_CONSOLE',
	'deadbeef7a6940e789848d3de3bedc0b',
	'deadbeef7a6940e789848d3de3bedc0b',
	'System Console Account',
	'SYSTEM_CONSOLE',
	'system'
);
INSERT INTO users (key, name, created_by, updated_by, full_name, password_hash, acl_role) VALUES (
	'feedfacead3e4cc6bd9c501224e24359',
	'SYSTEM_SIGNUP',
	'deadbeef7a6940e789848d3de3bedc0b',
	'deadbeef7a6940e789848d3de3bedc0b',
	'System Signup Account',
	'SYSTEM_SIGNUP',
	'system'
);

-- Create accounts tables
CREATE TABLE account_plans (
	key UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
	name VARCHAR NOT NULL UNIQUE
);
CREATE VIEW account_plans_view AS SELECT * FROM account_plans;
INSERT INTO account_plans (key, name) VALUES (
	'faceb0d54b6c4f91b60070193e133353',
	'Free'
);
CREATE TABLE accounts (
	key UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
	name VARCHAR NOT NULL UNIQUE,
	created_by UUID NOT NULL REFERENCES users(key),
	updated_by UUID NOT NULL REFERENCES users(key),
	created TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
	updated TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
	deleted TIMESTAMP WITH TIME ZONE,
	suspended TIMESTAMP WITH TIME ZONE,
	plan_key UUID NOT NULL default 'faceb0d54b6c4f91b60070193e133353'
);
ALTER TABLE users ADD COLUMN account_key UUID REFERENCES accounts(key);
CREATE VIEW accounts_view AS SELECT * FROM accounts;
CREATE VIEW users_view AS SELECT * FROM users;

-- Create tables for session and login management
CREATE TABLE sessions (
	id CHAR(32) NOT NULL,
	name CHAR(32) NOT NULL,
	modified INT NOT NULL,
	lifetime INT NOT NULL,
	data TEXT,
	PRIMARY KEY (id, name)
);
CREATE TABLE user_logins (
	name VARCHAR NOT NULL REFERENCES users(name),
	series UUID NOT NULL,
	token UUID NOT NULL,
	expiry INT NOT NULL,
	created TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
	PRIMARY KEY (name, series, token)
);

-- Create database version history table
CREATE TABLE migration_history (
	sequence INT PRIMARY KEY,
	name VARCHAR NOT NULL,
	created TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()
);
INSERT INTO migration_history (sequence, name) VALUES ('0', 'Migration000Init');
INSERT INTO log (level, tag, message) VALUES ('7', 'init', 'Database initialised');

COMMIT;
