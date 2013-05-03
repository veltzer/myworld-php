package org.meta.gwtworld.client;

import com.google.gwt.core.client.EntryPoint;
import com.google.gwt.user.client.ui.RootPanel;
import com.sencha.gxt.widget.core.client.button.TextButton;

public class Gwtworld implements EntryPoint {
	public void onModuleLoad() {
		RootPanel.get().add(new TextButton("Hello from gxt"));
	}
}
