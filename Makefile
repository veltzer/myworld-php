WP_DIR:=/var/www/blog
PLUGIN_DIR:=$(WP_DIR)/wp-content/plugins
THEME_DIR:=$(WP_DIR)/wp-content/themes
PLUGIN_NAME:=myworld
FULL_PLUGIN_DIR=$(PLUGIN_DIR)/$(PLUGIN_NAME)
THEME_NAME:=mytheme
FULL_THEME_DIR=$(THEME_DIR)/$(THEME_NAME)

.PHONY: all
all:

.PHONY: install
install:
	-sudo rm -rf $(FULL_PLUGIN_DIR)
	sudo cp -r $(PLUGIN_NAME) $(PLUGIN_DIR) 
	sudo cp -r include frag direct resources $(FULL_PLUGIN_DIR)
	sudo cp -r $(THEME_NAME) $(THEME_DIR)
