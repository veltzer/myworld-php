package org.meta.gwtworld.client.model;

import com.google.gwt.editor.client.Editor.Path;
import com.sencha.gxt.data.shared.LabelProvider;
import com.sencha.gxt.data.shared.ModelKeyProvider;
import com.sencha.gxt.data.shared.PropertyAccess;

public interface TbIdPersonProperties extends PropertyAccess<TbIdPerson> {
	@Path("id")
	ModelKeyProvider<TbIdPerson> key();
	
	@Path("fullname")
	LabelProvider<TbIdPerson> fullNameLabel();
	
}