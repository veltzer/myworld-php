include /usr/share/templar/Makefile

ALL:=$(TEMPLAR_ALL)
ALL_DEP:=$(TEMPLAR_ALL_DEP)

##############
# PARAMETERS #
##############
# target directory where all will be installed...
WEB_ROOT:=/var/www
# user to be used to access the application
attr.web_username:=mark
# blog directory within the target directory...
WP_DIR:=$(WEB_ROOT)/blog
# private directory...
WEB_DIR_PRIVATE:=$(WEB_ROOT)/private
# public directory...
WEB_DIR_PUBLIC:=$(WEB_ROOT)/public
# where are plugins to be installed in wordpress...
PLUGIN_DIR:=$(WP_DIR)/wp-content/plugins
# where are themes to be installed in wordpress...
THEME_DIR:=$(WP_DIR)/wp-content/themes
# do you want dependency on the makefile itself ?
DO_MAKEDEPS:=1
# do you want to see the commands executed ?
DO_MKDBG:=0
# do you want to compress javascript code ?
DO_JSCOMPRESS:=1
# output folder
OUT:=out

# tools
TOOL_COMPILER:=~/install/closure/compiler.jar
TOOL_JSMIN:=~/install/jsmin/jsmin
TOOL_JSDOC:=~/install/jsdoc/jsdoc
TOOL_JSL:=~/install/jsl/jsl
TOOL_GJSLINT:=gjslint
TOOL_YUICOMPRESSOR:=yui-compressor
TOOL_JSLINT:=jslint
TOOL_WRAPPER_QUIET:=scripts/wrapper_quiet.py

JSCHECK:=jscheck.stamp

########
# CODE #
########
ALL+=$(JSCHECK)
CLEAN:=$(JSCHECK)

# silent stuff
ifeq ($(DO_MKDBG),1)
Q:=
# we are not silent in this branch
else # DO_MKDBG
Q:=@
#.SILENT:
endif # DO_MKDBG

MYWORLD_PLUGIN_NAME:=myworld
MYWORLD_PLUGIN_FULL_DIR:=$(PLUGIN_DIR)/$(MYWORLD_PLUGIN_NAME)
MYWORLD_PLUGIN_FILES:=$(shell find $(MYWORLD_PLUGIN_NAME) -type f)
MYWORLD_PLUGIN_ZIP=$(OUT)/plugins/myworld.zip
CLEAN:=$(CLEAN) $(MYWORLD_PLUGIN_ZIP)
ALL+=$(MYWORLD_PLUGIN_ZIP)

MYHEB_PLUGIN_NAME:=myheb
MYHEB_PLUGIN_FULL_DIR:=$(PLUGIN_DIR)/$(MYHEB_PLUGIN_NAME)
MYHEB_PLUGIN_FILES:=$(shell find $(MYHEB_PLUGIN_NAME) -type f)
MYHEB_PLUGIN_ZIP=$(OUT)/plugins/myheb.zip
CLEAN:=$(CLEAN) $(MYHEB_PLUGIN_ZIP)
ALL+=$(MYHEB_PLUGIN_ZIP)

MYTHEME_THEME_NAME:=mytheme
MYTHEME_THEME_FULL_DIR:=$(THEME_DIR)/$(MYTHEME_THEME_NAME)
MYTHEME_THEME_FILES:=$(shell find $(MYTHEME_THEME_NAME) -type f)
MYTHEME_THEME_ZIP=$(OUT)/themes/mytheme.zip
CLEAN:=$(CLEAN) $(MYTHEME_THEME_ZIP)
ALL+=$(MYTHEME_THEME_ZIP)

SOURCES_JS:=$(shell find . -name "*.js")

CONFIG:=~/.myworld.php

#########
# RULES #
#########
.DEFAULT_GOAL=all
.PHONY: all
all: $(ALL)
$(MYHEB_PLUGIN_ZIP): $(MYHEB_PLUGIN_FILES) $(ALL_DEP)
	$(info doing [$@])
	$(Q)mkdir -p $(dir $@)
	$(Q)-rm -f $@
	$(Q)zip --quiet -r $@ $(MYHEB_PLUGIN_NAME)
$(MYWORLD_PLUGIN_ZIP): $(MYWORLD_PLUGIN_FILES) $(ALL_DEP)
	$(info doing [$@])
	$(Q)mkdir -p $(dir $@)
	$(Q)-rm -f $@
	$(Q)zip --quiet -r $@ $(MYWORLD_PLUGIN_NAME)
$(MYTHEME_THEME_ZIP): $(MYTHEME_THEME_FILES) $(ALL_DEP)
	$(info doing [$@])
	$(Q)mkdir -p $(dir $@)
	$(Q)-rm -f $@
	$(Q)zip --quiet -r $@ $(MYTHEME_THEME_NAME)
$(JSCHECK): $(SOURCES_JS) $(ALL_DEP) $(TOOL_WRAPPER_QUIET)
	$(info doing [$@])
	$(Q)$(TOOL_JSL) --conf=support/jsl.conf --quiet --nologo --nosummary --nofilelisting $(SOURCES_JS)
	$(Q)$(TOOL_WRAPPER_QUIET) $(TOOL_GJSLINT) --flagfile support/gjslint.cfg $(SOURCES_JS)
	$(Q)mkdir -p $(dir $@)
	$(Q)touch $(JSCHECK)

#	$(Q)$(TOOL_WRAPPER_QUIET) jshint --config support/jshint.conf $(SOURCES_JS)
#	$(Q)$(TOOL_WRAPPER_QUIET) jshint --config support/jshint.conf public/myworld_utils.js
#	$(Q)$(TOOL_WRAPPER_QUIET) jslint $(SOURCES_JS)
#	$(Q)$(TOOL_WRAPPER_QUIET) jslint public/myworld_utils.js

# list the plugins...
.PHONY: list
list:
	$(Q)zipinfo $(MYHEB_PLUGIN_ZIP)
	$(Q)zipinfo $(MYWORLD_PLUGIN_ZIP)
	$(Q)zipinfo $(MYTHEME_THEME_ZIP)

.PHONY: remake_password
remake_password:
	$(Q)htpasswd -bc private/.htpasswd $(attr.web_username) $(attr.web_password) 2> /dev/null # set security

.PHONY: remake_public_password
remake_public_password:
	$(Q)htpasswd -bc ~/public_html/.htpasswd $(attr.web_username) $(attr.web_password) 2> /dev/null # set security

.PHONY: install
install: all $(CONFIG)
	$(info doing [$@])
	$(Q)-sudo rm -rf $(MYHEB_PLUGIN_FULL_DIR)
	$(Q)-sudo rm -rf $(MYWORLD_PLUGIN_FULL_DIR)
	$(Q)-sudo rm -rf $(MYTHEME_THEME_FULL_DIR)
	$(Q)sudo cp -r $(MYHEB_PLUGIN_NAME) $(PLUGIN_DIR)
	$(Q)sudo cp -r $(MYWORLD_PLUGIN_NAME) $(PLUGIN_DIR)
	$(Q)sudo cp -r $(MYTHEME_THEME_NAME) $(THEME_DIR)
	$(Q)sudo cp $(CONFIG) $(PLUGIN_DIR)/$(MYWORLD_PLUGIN_NAME)/src/config.php
	$(Q)sudo cp misc/rss.png $(WP_DIR)/wp-includes/images/rss.png
	$(Q)sudo rm -rf $(WEB_DIR_PRIVATE) # remove the old folder
	$(Q)sudo cp -r private $(WEB_DIR_PRIVATE) # copy to the target
	$(Q)sudo cp $(MYWORLD_PLUGIN_NAME)/src/utils.php $(WEB_DIR_PRIVATE) # copy support code
	$(Q)sudo cp $(CONFIG) $(WEB_DIR_PRIVATE)/config.php # copy support code
	$(Q)sudo rm -rf $(WEB_DIR_PUBLIC) # remove the old folder
	$(Q)sudo cp -r public $(WEB_DIR_PUBLIC) # copy to the target
	$(Q)sudo cp $(MYWORLD_PLUGIN_NAME)/src/utils.php private/GetData.php private/GetMovies.php $(WEB_DIR_PUBLIC) # copy support code
	$(Q)sudo cp $(CONFIG) $(WEB_DIR_PUBLIC)/config.php # copy support code

.PHONY: check
check:
	$(info doing [$@])
	$(Q)scripts/wrapper_noerr.py git grep \'veltzer\'
	$(Q)scripts/wrapper_noerr.py git grep \'mark\'
	$(Q)scripts/wrapper_noerr.py git grep ' $$'
	$(Q)scripts/wrapper_noerr.py git grep '\s$$'

.PHONY: clean
clean:
	$(info doing [$@])
	$(Q)git clean -fxd > /dev/null

.PHONY: clean_manual
clean_manual:
	$(info doing [$@])
	$(Q)-rm -f $(CLEAN)

.PHONY: debug
debug:
	$(info ALL is $(ALL))
	$(info CLEAN is $(CLEAN))
	$(info WEB_ROOT is $(WEB_ROOT))
	$(info WP_DIR is $(WP_DIR))
	$(info attr.web_password is $(attr.web_password))
	$(info attr.web_username is $(attr.web_username))
	$(info SOURCES_JS is $(SOURCES_JS))

.PHONY: install_wp
install_wp:
	$(info doing [$@])
	$(Q)-sudo rm -rf $(WP_DIR)
	$(Q)sudo mkdir $(WP_DIR)
	$(Q)sudo chown $$USER.$$USER $(WP_DIR)
	$(Q)tar --extract --gunzip --directory $(WP_DIR) --file sources/wp/wordpress.tar.gz --strip-components=1
	$(Q)cp sources/wp-config.php $(WP_DIR)
	$(Q)for x in sources/plugins/*.zip; do unzip -q $$x -d $(WP_DIR)/wp-content/plugins; done
	$(Q)sudo chown -R root.root $(WP_DIR)
	$(info dont forget to make install and enable all plugins and configure them if needed...)

.PHONY: install_scripts
install_scripts:
	$(Q)scripts/install_scripts.py
