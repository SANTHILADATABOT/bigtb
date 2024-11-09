#!/bin/bash

# args
MSG=${1-'deploy from git'}
MAINFILE="cpm.php" # for version checking

# paths
SRC_DIR=$(git rev-parse --show-toplevel)
DIR_NAME=$(basename $SRC_DIR)
BUILD_DIR="$SRC_DIR/build"
ZIP_DIR="$SRC_DIR/zips"
ZIP_NAME="$DIR_NAME-$(date +%Y%m%d).zip"

# Ensure build and zip directories exist
mkdir -p $BUILD_DIR
mkdir -p $ZIP_DIR

# make sure we're deploying from the right dir
if [ ! -d "$SRC_DIR/.git" ]; then
    echo "$SRC_DIR doesn't seem to be a git repository"
    exit
fi

# check version in readme.txt is the same as plugin file
READMEVERSION=$(grep "Stable tag" $SRC_DIR/readme.txt | awk '{ print $NF}')
PLUGINVERSION=$(grep "Version" $SRC_DIR/$MAINFILE | awk '{ print $NF}')

echo ".........................................."
echo
echo "Preparing to deploy Advanced WP PM Plus"
echo "(Current version: $PLUGINVERSION)"
echo
echo ".........................................."
echo

if [ "$READMEVERSION" != "$PLUGINVERSION" ]; then
    echo "Versions don't match. Exiting....";
    exit 1
fi

echo "Copying necessary files to build directory..."
# Here, customize the copying as needed. This is a basic example.
rsync -av --exclude='.git' --exclude='node_modules' --exclude='build' --exclude='zips' $SRC_DIR/ $BUILD_DIR/

echo "Creating zip file..."
cd $ZIP_DIR
zip -r $ZIP_NAME "$BUILD_DIR/"

echo "Cleanup..."
rm -rf $BUILD_DIR

echo "Zip package created: $ZIP_DIR/$ZIP_NAME"
