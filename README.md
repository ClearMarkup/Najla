# Najla CLI commands
- `najla init` - to initialize the project
## Class
- `najla class:create <name>` - to create a new class
- `najla class:extend <classname> <name> [--replace]` - to create a new class that extends another class. The `--replace` flag is optional and it will replace all classes with the extended class name
## Route
- `najla route:api <method> <path>` - to create a new api route
## Build
- `najla build:run` - to run the build
- `najla build:add <file/dir>` - to add a file or directory to the build
- `najla build:remove <file/dir>` - to remove a file or directory from the build
## Server
- `najla server:mail` - to serve the mail server
- `najla server:web` - to serve the web server