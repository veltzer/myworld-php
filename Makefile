# parameters for this makefile...
#################################
# target directory where all will be installed...
WEB_ROOT:=/var/www
# user to be used to access the application
WEB_USER:=mark
# password (generated using makepasswd)
WEB_PASSWORD:=$(shell cat ~/.myworldrc | grep WEB_PASSWORD= | cut -d = -f 2)
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

# here starts the makefile...
#############################
ALL:=
CLEAN:=

# silent stuff
ifeq ($(DO_MKDBG),1)
Q:=
# we are not silent in this branch
else # DO_MKDBG
Q:=@
#.SILENT:
endif # DO_MKDBG

# handle dependency on the makefile itself...
ALL_DEP:=
ifeq ($(DO_MAKEDEPS),1)
	ALL_DEP:=$(ALL_DEP) Makefile
endif

MYWORLD_PLUGIN_NAME:=myworld
MYWORLD_PLUGIN_FULL_DIR:=$(PLUGIN_DIR)/$(MYWORLD_PLUGIN_NAME)
MYWORLD_PLUGIN_FILES:=$(shell find $(MYWORLD_PLUGIN_NAME) -type f)
MYWORLD_PLUGIN_ZIP=$(OUT)/plugins/myworld.zip
CLEAN:=$(CLEAN) $(MYWORLD_PLUGIN_ZIP)
ALL:=$(ALL) $(MYWORLD_PLUGIN_ZIP)

MYHEB_PLUGIN_NAME:=myheb
MYHEB_PLUGIN_FULL_DIR:=$(PLUGIN_DIR)/$(MYHEB_PLUGIN_NAME)
MYHEB_PLUGIN_FILES:=$(shell find $(MYHEB_PLUGIN_NAME) -type f)
MYHEB_PLUGIN_ZIP=$(OUT)/plugins/myheb.zip
CLEAN:=$(CLEAN) $(MYHEB_PLUGIN_ZIP)
ALL:=$(ALL) $(MYHEB_PLUGIN_ZIP)

MYTHEME_THEME_NAME:=mytheme
MYTHEME_THEME_FULL_DIR:=$(THEME_DIR)/$(MYTHEME_THEME_NAME)
MYTHEME_THEME_FILES:=$(shell find $(MYTHEME_THEME_NAME) -type f)
MYTHEME_THEME_ZIP=$(OUT)/themes/mytheme.zip
CLEAN:=$(CLEAN) $(MYTHEME_THEME_ZIP)
ALL:=$(ALL) $(MYTHEME_THEME_ZIP)

.PHONY: all
all: $(ALL)

$(MYHEB_PLUGIN_ZIP): $(MYHEB_PLUGIN_FILES) $(ALL_DEP)
	$(info doing [$@])
	$(Q)-mkdir -p $(dir $@)
	$(Q)-rm -f $@
	$(Q)zip --quiet -r $@ $(MYHEB_PLUGIN_NAME)
$(MYWORLD_PLUGIN_ZIP): $(MYWORLD_PLUGIN_FILES) $(ALL_DEP)
	$(info doing [$@])
	$(Q)-mkdir -p $(dir $@)
	$(Q)-rm -f $@
	$(Q)zip --quiet -r $@ $(MYWORLD_PLUGIN_NAME)
$(MYTHEME_THEME_ZIP): $(MYTHEME_THEME_FILES) $(ALL_DEP)
	$(info doing [$@])
	$(Q)-mkdir -p $(dir $@)
	$(Q)-rm -f $@
	$(Q)zip --quiet -r $@ $(MYTHEME_THEME_NAME)

# list the plugins...
.PHONY: list
list:
	$(Q)zipinfo $(MYHEB_PLUGIN_ZIP)
	$(Q)zipinfo $(MYWORLD_PLUGIN_ZIP)
	$(Q)zipinfo $(MYTHEME_THEME_ZIP)

.PHONY: remake_password
remake_password:
	$(Q)htpasswd -bc private/.htpasswd $(WEB_USER) $(WEB_PASSWORD) 2> /dev/null # set security

.PHONY: remake_public_password
remake_public_password:
	$(Q)htpasswd -bc ~/public_html/.htpasswd $(WEB_USER) $(WEB_PASSWORD) 2> /dev/null # set security

.PHONY: install
install: all
	$(info doing [$@])
	$(Q)-sudo rm -rf $(MYHEB_PLUGIN_FULL_DIR)
	$(Q)sudo cp -r $(MYHEB_PLUGIN_NAME) $(PLUGIN_DIR)
	$(Q)#sudo chown www-data.www-data $(PLUGIN_DIR)/$(MYHEB_PLUGIN_NAME)
	$(Q)sudo chmod -R go+rx $(PLUGIN_DIR)/$(MYHEB_PLUGIN_NAME)
	$(Q)-sudo rm -rf $(MYWORLD_PLUGIN_FULL_DIR)
	$(Q)sudo cp -r $(MYWORLD_PLUGIN_NAME) $(PLUGIN_DIR)
	$(Q)#sudo chown www-data.www-data $(PLUGIN_DIR)/$(MYWORLD_PLUGIN_NAME)
	$(Q)sudo chmod -R go+rx $(PLUGIN_DIR)/$(MYWORLD_PLUGIN_NAME)
	$(Q)-sudo rm -rf $(MYTHEME_THEME_FULL_DIR)
	$(Q)sudo cp -r $(MYTHEME_THEME_NAME) $(THEME_DIR)
	$(Q)#sudo chown www-data.www-data $(THEME_DIR)/$(MYTHEME_THEME_NAME)
	$(Q)sudo chmod -R go+rx $(THEME_DIR)/$(MYTHEME_THEME_NAME)
	$(Q)sudo cp misc/rss.png $(WP_DIR)/wp-includes/images/rss.png
	$(Q)# now install the private folder
	$(Q)sudo rm -rf $(WEB_DIR_PRIVATE) # remove the old folder
	$(Q)sudo cp -r private $(WEB_DIR_PRIVATE) # copy to the target
	$(Q)sudo cp $(MYWORLD_PLUGIN_NAME)/src/utils.php $(WEB_DIR_PRIVATE) # copy support code
	$(Q)sudo chmod -R go+rx $(WEB_DIR_PRIVATE)
	$(Q)# now install the public folder
	$(Q)sudo rm -rf $(WEB_DIR_PUBLIC) # remove the old folder
	$(Q)sudo cp -r public $(WEB_DIR_PUBLIC) # copy to the target
	$(Q)sudo cp $(MYWORLD_PLUGIN_NAME)/src/utils.php private/GetData.php private/GetMovies.php $(WEB_DIR_PUBLIC) # copy support code
	$(Q)sudo chmod -R go+rx $(WEB_DIR_PUBLIC)

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
	$(info WEB_PASSWORD is $(WEB_PASSWORD))

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

# TODO: in 'install_scripts' remove all symlinks in ~/install/bin that already point to these scripts
.PHONY: install_scripts
install_scripts:
	$(Q)for x in scripts/*; do ln -fs $$PWD/$$x ~/install/bin/`basename $$x`;done

.PHONY: install_perl
install_perl:
	$(Q)for x in perl/*.pm; do ln -fs $$PWD/$$x ~/install/myperl/`basename $$x`;done
.PHONY: install_python
install_python:
	$(Q)rm -rf ~/install/mypython/*
	$(Q)cp -r python/* ~/install/mypython
