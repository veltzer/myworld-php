##############
# parameters #
##############
# do you want to see the commands executed ?
DO_MKDBG:=0
# do you want to compress javascript code ?
DO_JSCOMPRESS:=1
# output folder
OUT:=out
# do you want dependency on the Makefile itself ?
DO_ALLDEP:=1
# do you want to check the javascript files?
DO_JSCHECK:=0

# tools
TOOL_COMPILER:=tools/compiler.jar
TOOL_JSMIN:=tools/jsmin/jsmin
TOOL_JSDOC:=tools/jsdoc/jsdoc
TOOL_JSL:=tools/jsl/jsl
TOOL_GJSLINT:=gjslint
TOOL_YUICOMPRESSOR:=yui-compressor
TOOL_JSLINT:=jslint

JSCHECK:=jscheck.stamp

########
# code #
########
# target directory where all will be installed...
WEB_ROOT:=/var/www
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

ALL:=

ifeq ($(DO_JSCHECK),1)
ALL+=$(JSCHECK)
endif # DO_JSCHECK

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
ALL+=$(MYWORLD_PLUGIN_ZIP)

MYHEB_PLUGIN_NAME:=myheb
MYHEB_PLUGIN_FULL_DIR:=$(PLUGIN_DIR)/$(MYHEB_PLUGIN_NAME)
MYHEB_PLUGIN_FILES:=$(shell find $(MYHEB_PLUGIN_NAME) -type f)
MYHEB_PLUGIN_ZIP=$(OUT)/plugins/myheb.zip
ALL+=$(MYHEB_PLUGIN_ZIP)

MYTHEME_THEME_NAME:=mytheme
MYTHEME_THEME_FULL_DIR:=$(THEME_DIR)/$(MYTHEME_THEME_NAME)
MYTHEME_THEME_FILES:=$(shell find $(MYTHEME_THEME_NAME) -type f)
MYTHEME_THEME_ZIP=$(OUT)/themes/mytheme.zip
ALL+=$(MYTHEME_THEME_ZIP)

SOURCES_JS:=$(shell find public -type f -and -name "*.js")

CONFIG:=~/.myworld.php

#########
# rules #
#########
.PHONY: all
all: $(ALL)
	@true

$(MYHEB_PLUGIN_ZIP): $(MYHEB_PLUGIN_FILES)
	$(info doing [$@])
	$(Q)mkdir -p $(dir $@)
	$(Q)-rm -f $@
	$(Q)zip --quiet -r $@ $(MYHEB_PLUGIN_NAME)
$(MYWORLD_PLUGIN_ZIP): $(MYWORLD_PLUGIN_FILES)
	$(info doing [$@])
	$(Q)mkdir -p $(dir $@)
	$(Q)-rm -f $@
	$(Q)zip --quiet -r $@ $(MYWORLD_PLUGIN_NAME)
$(MYTHEME_THEME_ZIP): $(MYTHEME_THEME_FILES)
	$(info doing [$@])
	$(Q)mkdir -p $(dir $@)
	$(Q)-rm -f $@
	$(Q)zip --quiet -r $@ $(MYTHEME_THEME_NAME)
$(JSCHECK): $(SOURCES_JS)
	$(info doing [$@])
	$(Q)jsl --conf=support/jsl.conf --quiet --nologo --nosummary --nofilelisting $(SOURCES_JS)
	$(Q)pymakehelper only_print_on_error $(TOOL_GJSLINT) --flagfile support/gjslint.cfg $(SOURCES_JS)
	$(Q)mkdir -p $(dir $@)
	$(Q)touch $(JSCHECK)

#	$(Q)pymakehelper only_print_on_error jshint $(SOURCES_JS)
#	$(Q)pymakehelper only_print_on_error jshint public/myworld_utils.js
#	$(Q)pymakehelper only_print_on_error jslint $(SOURCES_JS)
#	$(Q)pymakehelper only_print_on_error jslint public/myworld_utils.js

# list the plugins...
.PHONY: list
list:
	$(Q)zipinfo $(MYHEB_PLUGIN_ZIP)
	$(Q)zipinfo $(MYWORLD_PLUGIN_ZIP)
	$(Q)zipinfo $(MYTHEME_THEME_ZIP)

.PHONY: remake_password
remake_password:
	$(Q)htpasswd -bc private/.htpasswd $(tdefs.web_username) $(tdefs.web_password) 2> /dev/null # set security

.PHONY: remake_public_password
remake_public_password:
	$(Q)htpasswd -bc ~/public_html/.htpasswd $(tdefs.web_username) $(tdefs.web_password) 2> /dev/null # set security


.PHONY: install_private
install_private:
	$(info doing [$@])
	$(Q)sudo rm -rf $(WEB_DIR_PRIVATE) # remove the old folder
	$(Q)sudo cp -r private $(WEB_DIR_PRIVATE) # copy to the target
	$(Q)sudo cp $(MYWORLD_PLUGIN_NAME)/src/utils.php $(WEB_DIR_PRIVATE) # copy support code
	$(Q)sudo cp $(CONFIG) $(WEB_DIR_PRIVATE)/config.php # copy support code

.PHONY: install_site
install_site: all $(CONFIG)
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
	$(Q)pymakehelper only_print_on_error git grep \'veltzer\'
	$(Q)pymakehelper only_print_on_error git grep \'mark\'
	$(Q)pymakehelper only_print_on_error git grep ' $$'
	$(Q)pymakehelper only_print_on_error git grep '\s$$'

.PHONY: clean_hard
clean_hard:
	$(info doing [$@])
	$(Q)git clean -qffxd

.PHONY: clean_manual
clean_manual:
	$(info doing [$@])
	$(Q)-rm -f $(ALL)

.PHONY: debug_full
debug_full:
	$(info ALL is $(ALL))
	$(info WEB_ROOT is $(WEB_ROOT))
	$(info WP_DIR is $(WP_DIR))
	$(info tdefs.web_password is $(tdefs.web_password))
	$(info tdefs.web_username is $(tdefs.web_username))
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

##########
# alldep #
##########
ifeq ($(DO_ALLDEP),1)
.EXTRA_PREREQS+=$(foreach mk, ${MAKEFILE_LIST},$(abspath ${mk}))
endif # DO_ALLDEP
