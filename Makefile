WP_DIR=/var/www/blog
PLUGIN_DIR=wp-content/plugins
PLUGIN_NAME=myworld

.PHONY: install
install:
	-sudo rm -rf $(WP_DIR)/$(PLUGIN_DIR)/$(PLUGIN_NAME)
	sudo cp -r $(PLUGIN_NAME) $(WP_DIR)/$(PLUGIN_DIR) 
