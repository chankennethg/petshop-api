# Pet Shop API

Backend API for Pet Shop (eCommerce)

## Prerequisite
This project utilizes a Makefile to automate builds and tasks.

If you dont have make in your machine you can copy paste the commands in Makefile

You may run `make help` to list available commands. 

## Setup
1. Clone this repo
2. Generate SSL Keys by running `make jwt-key`
3. Generate .env file via `make copy-env`
4. Start docker services via `make start`
5. Run `make init` . This will install dependencies and create/seed database.

## Checking code standards (PHPStan & PHPInsights)

```bash
make standards
```

### Auto fixing issues

```bash
make lint-fix
```

## Running the test cases

```bash
make test
```

## User Stories Implemented
- API prefix convention
- Bearer Token authentication
- Middleware protection
- Admin endpoints (All users listing (non-admins), Edit and Delete user’s accounts)
- Main page endpoints
- Files uploads
- Listing feature

