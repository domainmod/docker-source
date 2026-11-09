This repository contains the source code for our official DomainMOD Docker container on Docker Hub:

<https://hub.docker.com/r/domainmod/domainmod>

Each image is built from the DomainMOD release it's tagged with: the build clones DomainMOD from GitHub at that release tag, so the application code isn't kept in this repository.

## Optional encrypted database connections

DomainMOD 4.24.0 and later can encrypt the connection to the database and check the server's certificate. Set these environment variables to add the matching settings to `config.inc.php`:

- `DOMAINMOD_DB_SSL_MODE`: `auto` (the default when it's not set), `verify`, or `off`
- `DOMAINMOD_DB_SSL_CA`: for `verify`, the path inside the container to the CA certificate that signed the database server's certificate (mount it into the container); the system's trusted CAs are used when it's not set

See the User Guide for details: <https://domainmod.org/docs/userguide/database-encryption/>
