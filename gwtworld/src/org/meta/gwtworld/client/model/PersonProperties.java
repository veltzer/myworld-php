package org.meta.gwtworld.client.model;

import com.google.gwt.editor.client.Editor.Path;
import com.sencha.gxt.data.shared.LabelProvider;
import com.sencha.gxt.data.shared.ModelKeyProvider;
import com.sencha.gxt.data.shared.PropertyAccess;

public interface PersonProperties extends PropertyAccess<Person> {
	@Path("id")
	ModelKeyProvider<Person> key();
	
	@Path("fullName")
	LabelProvider<Person> fullNameLabel();
	
}