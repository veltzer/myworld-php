WP_DIR=/var/www/blog
PLUGIN_DIR=wp-content/plugins
PLUGIN_NAME=myworld

.PHONY: install
install:
	-sudo rm -rf $(WP_DIR)/$(PLUGIN_DIR)/$(PLUGIN_NAME)
	sudo cp -r $(PLUGIN_NAME) $(WP_DIR)/$(PLUGIN_DIR) 
	sudo mkdir $(WP_DIR)/$(PLUGIN_DIR)/$(PLUGIN_NAME)/include
	sudo cp -r include frag direct $(WP_DIR)/$(PLUGIN_DIR)/$(PLUGIN_NAME)
