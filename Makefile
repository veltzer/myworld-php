WEB_ROOT:=/var/www
WP_DIR:=$(WEB_ROOT)/blog
PLUGIN_DIR:=$(WP_DIR)/wp-content/plugins
THEME_DIR:=$(WP_DIR)/wp-content/themes

ALL:=
CLEAN:=

PLUGIN_NAME:=myworld
FULL_PLUGIN_DIR:=$(PLUGIN_DIR)/$(PLUGIN_NAME)
FILES_MYWORLD:=$(shell find myworld -type f)
CLEAN:=$(CLEAN) target/plugins/myworld.zip
ALL:=$(ALL) target/plugins/myworld.zip

THEME_NAME:=mytheme
FULL_THEME_DIR:=$(THEME_DIR)/$(THEME_NAME)
FILES_MYTHEME:=$(shell find mytheme -type f)
CLEAN:=$(CLEAN) target/themes/mytheme.zip
ALL:=$(ALL) target/themes/mytheme.zip

.PHONY: all
all: $(ALL)

target/plugins/myworld.zip: $(FILES_MYWORLD)
	zip -r $@ $(PLUGIN_NAME)
target/themes/mytheme.zip: $(FILES_MYTHEME)
	zip -r $@ $(THEME_NAME)

.PHONY: install
install:
	-sudo rm -rf $(FULL_PLUGIN_DIR) $(FULL_THEME_DIR)
	sudo cp -r $(PLUGIN_NAME) $(PLUGIN_DIR) 
	sudo cp -r sa/* $(FULL_PLUGIN_DIR)
	sudo cp -r $(THEME_NAME) $(THEME_DIR)
	sudo cp misc/rss.png $(WP_DIR)/wp-includes/images/rss.png
	sudo cp misc/favicon.ico $(WEB_ROOT)
	sudo cp misc/htaccess $(WEB_ROOT)/.htaccess

.PHONY: clean
clean:
	-rm -f $(CLEAN) 

.PHONY: debug
debug:
	$(info ALL is $(ALL))
	$(info CLEAN is $(CLEAN))
