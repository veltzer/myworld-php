WEB_ROOT:=/var/www
WP_DIR:=$(WEB_ROOT)/blog
PLUGIN_DIR:=$(WP_DIR)/wp-content/plugins
THEME_DIR:=$(WP_DIR)/wp-content/themes

ALL:=
CLEAN:=

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

$(MYHEB_PLUGIN_ZIP): $(MYHEB_PLUGIN_FILES)
	zip --quiet -r $@ $(MYHEB_PLUGIN_NAME)
$(MYWORLD_PLUGIN_ZIP): $(MYWORLD_PLUGIN_FILES)
	zip --quiet -r $@ $(MYWORLD_PLUGIN_NAME)
$(MYTHEME_THEME_ZIP): $(MYTHEME_THEME_FILES)
	zip --quiet -r $@ $(MYTHEME_THEME_NAME)

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

.PHONY: clean
clean:
	-rm -f $(CLEAN) 

.PHONY: debug
debug:
	$(info ALL is $(ALL))
	$(info CLEAN is $(CLEAN))
