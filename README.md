# ClearMarkup CLI commands
- `ClearMarkup init` - to initialize the project
## Class
- `ClearMarkup class:create <name>` - to create a new class
- `ClearMarkup class:extend <classname> <name> [--replace]` - to create a new class that extends another class. The `--replace` flag is optional and it will replace all classes with the extended class name
## Route
- `ClearMarkup route:api <method> <path>` - to create a new api route
## Build
- `ClearMarkup build:run` - to run the build
- `ClearMarkup build:add <file/dir>` - to add a file or directory to the build
- `ClearMarkup build:remove <file/dir>` - to remove a file or directory from the build
## Server
- `ClearMarkup server:mail` - to serve the mail server
- `ClearMarkup server:web` - to serve the web server