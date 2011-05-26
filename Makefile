# parameters for this makefile...
# target directory where all will be installed...
WEB_ROOT:=/var/www
# user to be used to access the application
WEB_USER:=mark
# password (generated using makepasswd)
WEB_PASSWORD:=MrGQ0GwhH
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

# here starts the makefile...
ALL:=
CLEAN:=

# handle dependency on the makefile itself...
ALL_DEP:=
ifeq ($(DO_MAKEDEPS),1)
	ALL_DEP:=$(ALL_DEP) Makefile
endif

MYWORLD_PLUGIN_NAME:=myworld
MYWORLD_PLUGIN_FULL_DIR:=$(PLUGIN_DIR)/$(MYWORLD_PLUGIN_NAME)
MYWORLD_PLUGIN_FILES:=$(shell find $(MYWORLD_PLUGIN_NAME) -type f)
MYWORLD_PLUGIN_ZIP=target/plugins/myworld.zip
CLEAN:=$(CLEAN) $(MYWORLD_PLUGIN_ZIP)
ALL:=$(ALL) $(MYWORLD_PLUGIN_ZIP)

MYHEB_PLUGIN_NAME:=myheb
MYHEB_PLUGIN_FULL_DIR:=$(PLUGIN_DIR)/$(MYHEB_PLUGIN_NAME)
MYHEB_PLUGIN_FILES:=$(shell find $(MYHEB_PLUGIN_NAME) -type f)
MYHEB_PLUGIN_ZIP=target/plugins/myheb.zip
CLEAN:=$(CLEAN) $(MYHEB_PLUGIN_ZIP)
ALL:=$(ALL) $(MYHEB_PLUGIN_ZIP)

MYTHEME_THEME_NAME:=mytheme
MYTHEME_THEME_FULL_DIR:=$(THEME_DIR)/$(MYTHEME_THEME_NAME)
MYTHEME_THEME_FILES:=$(shell find $(MYTHEME_THEME_NAME) -type f)
MYTHEME_THEME_ZIP=target/themes/mytheme.zip
CLEAN:=$(CLEAN) $(MYTHEME_THEME_ZIP)
ALL:=$(ALL) $(MYTHEME_THEME_ZIP)

.PHONY: all
all: $(ALL)

$(MYHEB_PLUGIN_ZIP): $(MYHEB_PLUGIN_FILES) $(ALL_DEP)
	-rm -f $@
	zip --quiet -r $@ $(MYHEB_PLUGIN_NAME)
$(MYWORLD_PLUGIN_ZIP): $(MYWORLD_PLUGIN_FILES) $(ALL_DEP)
	-rm -f $@
	zip --quiet -r $@ $(MYWORLD_PLUGIN_NAME)
$(MYTHEME_THEME_ZIP): $(MYTHEME_THEME_FILES) $(ALL_DEP)
	-rm -f $@
	zip --quiet -r $@ $(MYTHEME_THEME_NAME)

# list the plugins...
.PHONY: list
list:
	zipinfo $(MYHEB_PLUGIN_ZIP)
	zipinfo $(MYWORLD_PLUGIN_ZIP)
	zipinfo $(MYTHEME_THEME_ZIP)

.PHONY: remake_password
remake_password:
	htpasswd -bc private/.htpasswd $(WEB_USER) $(WEB_PASSWORD) 2> /dev/null # set security

.PHONY: install
install:
	-sudo rm -rf $(MYHEB_PLUGIN_FULL_DIR)
	sudo cp -r $(MYHEB_PLUGIN_NAME) $(PLUGIN_DIR)
	-sudo rm -rf $(MYWORLD_PLUGIN_FULL_DIR)
	sudo cp -r $(MYWORLD_PLUGIN_NAME) $(PLUGIN_DIR)
	-sudo rm -rf $(MYTHEME_THEME_FULL_DIR)
	sudo cp -r $(MYTHEME_THEME_NAME) $(THEME_DIR)
	sudo cp misc/rss.png $(WP_DIR)/wp-includes/images/rss.png
	sudo cp misc/htaccess $(WEB_ROOT)/.htaccess
	# now install the private folder
	sudo rm -rf $(WEB_DIR_PRIVATE) # remove the old folder
	sudo cp -r private $(WEB_DIR_PRIVATE) # copy to the target
	sudo cp $(MYWORLD_PLUGIN_NAME)/src/* $(WEB_DIR_PRIVATE) # copy support code
	# now install the public folder
	sudo rm -rf $(WEB_DIR_PUBLIC) # remove the old folder
	sudo cp -r public $(WEB_DIR_PUBLIC) # copy to the target

.PHONY: clean
clean:
	-rm -f $(CLEAN)

.PHONY: debug
debug:
	$(info ALL is $(ALL))
	$(info CLEAN is $(CLEAN))
