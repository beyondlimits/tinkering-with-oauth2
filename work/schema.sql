/*
CREATE TABLE users (
    id        SERIAL NOT NULL,
    name      VARCHAR(255) NOT NULL,
    PRIMARY KEY(id),
    UNIQUE(email)
);
*/

CREATE TABLE clients (
    id                     SERIAL NOT NULL,
--  user_id                INTEGER, -- account!
    name                   VARCHAR(255) NOT NULL,
    secret                 VARCHAR(100) NOT NULL,
    redirect               TEXT NOT NULL,
--  personal_access_client BOOLEAN NOT NULL,
--  password_client        BOOLEAN NOT NULL,
    revoked                BOOLEAN NOT NULL DEFAULT FALSE,
--  created_at             TIMESTAMP WITHOUT TIME ZONE,
--  updated_at             TIMESTAMP WITHOUT TIME ZONE,
    PRIMARY KEY(id)
);

/*
CREATE TABLE access_tokens (
    id         VARCHAR(100) NOT NULL,
    user_id    INTEGER,
    client_id  INTEGER NOT NULL,
    name       VARCHAR(255),
    scopes     TEXT,
    revoked    BOOLEAN NOT NULL,
    expires_at TIMESTAMP WITHOUT TIME ZONE,
    PRIMARY KEY(id),
    FOREIGN KEY(client_id)
        REFERENCES clients(id)
);
*/

/*
CREATE TABLE refresh_tokens (
    id              VARCHAR(100) NOT NULL,
    access_token_id VARCHAR(100) NOT NULL,
    revoked         BOOLEAN NOT NULL,
    expires_at      TIMESTAMP WITHOUT TIME ZONE,
    PRIMARY KEY(id),
    FOREIGN KEY(access_token_id)
        REFERENCES access_tokens(id)
);
*/

/*
CREATE TABLE personal_access_clients (
    id         SERIAL NOT NULL,
    client_id  INTEGER NOT NULL,
    created_at TIMESTAMP WITHOUT TIME ZONE,
    updated_at TIMESTAMP WITHOUT TIME ZONE,
    PRIMARY KEY(id),
    FOREIGN KEY(client_id)
        REFERENCES clients(client_id)
);
*/

CREATE TABLE auth_codes (
    id         SERIAL NOT NULL,
--  user_id    INTEGER NOT NULL,
    client_id  INTEGER NOT NULL,
    scopes     TEXT,
    revoked    BOOLEAN NOT NULL,
    expires_at TIMESTAMP WITHOUT TIME ZONE,
    PRIMARY KEY(id)
);
