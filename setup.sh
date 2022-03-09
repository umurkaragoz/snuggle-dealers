#!/usr/bin/env bash

# setup.sh
# formerly generate.sh
#
# Authors
# - Aykut CAN<<EON
# - Umur Karag0z
# - Yossi Cohen
#
# v2.11 3.2018
# v2.50 8.2020
# v3.00 1.2022

RS="\033[0m"        # reset
HC="\033[1m"        # hicolor
UL="\033[4m"        # underline
INV="\033[7m"       # inverse background and foreground

FBLACK="\033[30m"   # foreground black
FRED="\033[1;31m"   # foreground red
FGREEN="\033[32m"   # foreground green
FYELLOW="\033[33m"  # foreground yellow
FBLUE="\033[34m"    # foreground blue
FMAGENTA="\033[35m" # foreground magenta
FCYAN="\033[36m"    # foreground cyan
FWHITE="\033[37m"   # foreground white
FBROWN="\033[33m"   # foreground brown
FDGRAY="\033[30m"   # foreground dark gray

BBLACK="\033[40m"   # background black
BRED="\033[41m"     # background red
BGREEN="\033[42m"   # background green
BYELLLOW="\033[43m" # background yellow
BBLUE="\033[44m"    # background blue
BMAGENTA="\033[45m" # background magenta
BCYAN="\033[46m"    # background cyan
BWHITE="\033[47m"   # background white
BBROWN="\033[43m"   # background brown

# --------------------------------------------------------------------------------------------------------------------------------- get Options [-] -#
database=false
migrate=false
fresh=false
seed=false
create=false
purge=false
link=false
numArgs="$#"

# -------------------------------------------------------------------------------------------------------------------------------- show Message [-] -#
# Show a message in a box.
# Show a single line message in desired text style.
# A line consisting of spaces with the same length of the message will be added before and after the message.
# Example usage: `showMessage "${1}" "${BBROWN}${FDGRAY}"`
showMessage() {
    # Add some horizontal padding.
    message="  ${1}  "
    # Get the message character size.
    size=${#message}
    # Create a string of spaces with the same length as the message line.
    spacer=$(printf ' %.0s' $(seq 1 $size))
    # Get the desired text style from the second argument.
    style="${2}"

    echo -e "\n${style}${spacer}${RS}"
    echo -e "${style}${message}${RS}"
    echo -e "${style}${spacer}${RS}\n"
}

# -------------------------------------------------------------------------------------------------------------------------------- show Warning [-] -#
showWarning() {
    showMessage "${1}" "${BBROWN}${FDGRAY}"
}

# ---------------------------------------------------------------------------------------------------------------------------------- show Error [-] -#
showError() {
    showMessage "${1}" "${BRED}${FBLACK}"
}

# -------------------------------------------------------------------------------------------------------------------------- show Help Messages [-] -#
showHelpMessages() {
    local message=" Options:
      -d    create the database (set credentials on the .env file first!)
      -m    Migrate the database
      -f    Drop all the tables, and then migrate the database
      -s    Seed the database
      -c    Create required directories
      -l    Create required symlinks
      -p    Purge writeable directories clean
      -h    Show this help message
    "

    echo "$message"
}

showWarning "Ancient Book of Wizardry for Installation Purposes"

# --------------------------------------------------------------------------------------------------------------------------- initial Processes [-] -#

# check if .env file exists
if [ ! -f ./.env ]; then
    if [ ! -f ./.env.example ]; then
        echo -e "\n${FRED}Both '.env' and '.env.example' was not found! \nProecess shall be terminated.${RS}\n"
    else
        echo -e "\n${FRED}'.env' was not found, creating from '.env.example'.${RS}\n"
        # copy .env.example
        cp ./.env.example ./.env

        # generate application key
        echo -e "\n> php artisan key:generate"
        php artisan key:generate
    fi
fi

if [ "$numArgs" = "0" ]; then
    showHelpMessages
    exit 1
fi

# Mute getopts invalid option message, as we will show our own error message.
# https://stackoverflow.com/a/35535937/4306828
OPTERR=0
while getopts "dmfscphl" opt; do
    case $opt in
    d) database=true ;;
    m) migrate=true ;;
    f) fresh=true ;;
    s) seed=true ;;
    c) create=true ;;
    p) purge=true ;;
    l) link=true ;;
    h)
        showHelpMessages
        exit 1
        ;;
    ?)
        # Unrecognized option. Show a custom error.
        n=$(($OPTIND - 1)) # get current option index
        optionName=${!n}   # get the value of the variable "$n", which is the input's name
        showError "Invalid option \"$optionName\""
        showHelpMessages
        exit 1
        ;;
    esac
done

# Read the .env file and export keys as shell variables.
export $(grep -v '^#' .env | xargs)

if [ "$APP_ENV" = "production" ]; then
    showError "The app is in production! Wizardry shall not continue!"
    exit 1
fi

# ------------------------------------------------------------------------------------------------------------------------- create the Database [-] -#
if [ "$database" = true ]; then

    echo -e "\n${FBROWN}Creating the database...${RS}\n"

    mysql -v --user="$DB_USERNAME" --password="$DB_PASSWORD" --execute="CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\`"

    echo "done."
fi

# ----------------------------------------------------------------------------------------------------------------- setup Writeable Directories [-] -#
if [ "$create" = true ] || [ "$purge" = true ]; then

    echo -e "\n${FBROWN}Setting up required directories...${RS}\n"

    folders=(
        "./public"
        "./public/upload"
        "./storage"
        "./storage/logs"
        "./storage/temp"
        "./storage/app"
        "./storage/app/public"
        "./storage/temp/img"
        "./storage/temp/doc"
        "./storage/framework"
        "./storage/framework/cache"
        "./storage/framework/cache/data"
        "./storage/framework/sessions"
        "./storage/framework/views"
        "./storage/upload"
        "./storage/upload/img"
        "./storage/upload/doc"
        "./storage/upload-original"
        "./storage/upload-original/doc"
        "./storage/upload-original/img"
        "./bootstrap"
        "./bootstrap/cache"
    )

    for folder in "${folders[@]}"; do
        echo -n "==>" "$folder" : " "
        if ! [ -d "$folder" ]; then
            # directory does not exists, create it
            mkdir "$folder"
            echo -n " +CREATED "
        fi

        # CHMODify the directory
        chmod 777 -R "$folder"

        echo "    OK"
    done
fi
# ----------------------------------------------------------------------------------------------------------------- purge Writeable Directories [-] -#
if [ "$purge" = true ]; then

    echo -e "\n${FBROWN}Cleaning writeable directories...${RS}\n"

    emptyFolders=(
        "./storage/upload"
        "./storage/upload/img"
        "./storage/upload/doc"
        "./storage/upload-original"
        "./storage/upload-original/doc"
        "./storage/temp/img"
        "./storage/temp/doc"
    )

    for folder in "${emptyFolders[@]}"; do
        echo -n "==>" "$folder" : " "
        if [ -d "$folder" ]; then
            # Directory exists
            # 1- Remove the directory
            rm -Rf "$folder"
            # 2 - Create It
            mkdir "$folder"
            echo -n " +TRUNCATED "
        else
            # Directory does not exists
            # 1 - Create
            mkdir "$folder"
            # 2 - CHMODify
            chmod 777 "$folder"
            echo -n " +CREATED "
        fi

        # 2- CHMODify
        chmod 777 -R "$folder"
        echo "    OK"
    done

fi

if [ "$link" = true ]; then

    echo -e "\n${FBROWN}Creating necessary symlinks...${RS}\n"

    echo "Creating link: ./storage/upload --> ./public/upload..."

    source="$(pwd)/storage/upload"
    link="$(pwd)/public/upload"

    if [ "$OSTYPE" == "msys" ] || [ "$OSTYPE" == "linux-musl" ] || [ "$OSTYPE" == "win32" ]; then

        showError "Can't create links on Windows."

        echo -e "Run the CMD as administrator and execute the following command on the project root directory:"
        echo -e "${BBROWN}${FBLACK}cd public && mklink /d upload \"..\storage\upload\" && pause${RS}"

    else
        # Symbolic, relative paths, force in case already exists.
        if [ -L "$link" ]; then
            echo "$link already exists"
        else
            ln -sv $source $link
        fi
    fi

fi

# ------------------------------------------------------------------------------------------------------------------------------ final Commands [-] -#

if [ "$fresh" = true ]; then
    echo -e "\n>php artisan migrate:fresh"
    php artisan migrate:fresh
elif [ "$migrate" = true ]; then
    echo -e "\n> php artisan migrate"
    php artisan migrate
fi

if [ "$seed" = true ]; then
    echo -e "\n> php artisan db:seed"
    php artisan db:seed
fi
