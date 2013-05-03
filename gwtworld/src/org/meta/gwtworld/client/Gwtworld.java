package org.meta.gwtworld.client;

import java.util.List;

import org.meta.gwtworld.client.model.TbIdPersonProperties;
import org.meta.gwtworld.client.model.TbIdPerson;

import com.google.gwt.core.client.EntryPoint;
import com.google.gwt.core.client.GWT;
import com.google.gwt.event.logical.shared.ValueChangeEvent;
import com.google.gwt.event.logical.shared.ValueChangeHandler;
import com.google.gwt.user.client.rpc.AsyncCallback;
import com.google.gwt.user.client.ui.RootPanel;
import com.google.gwt.user.client.ui.Widget;
import com.sencha.gxt.data.shared.ListStore;
import com.sencha.gxt.widget.core.client.ContentPanel;
import com.sencha.gxt.widget.core.client.FramedPanel;
import com.sencha.gxt.widget.core.client.TabPanel;
import com.sencha.gxt.widget.core.client.container.BorderLayoutContainer;
import com.sencha.gxt.widget.core.client.container.BorderLayoutContainer.BorderLayoutData;
import com.sencha.gxt.widget.core.client.container.VerticalLayoutContainer;
import com.sencha.gxt.widget.core.client.container.VerticalLayoutContainer.VerticalLayoutData;
import com.sencha.gxt.widget.core.client.form.ComboBox;
import com.sencha.gxt.widget.core.client.form.FieldLabel;
import com.sencha.gxt.widget.core.client.info.Info;

public class Gwtworld implements EntryPoint {
	public void onModuleLoad() {
		/*
		final ContentPanel west=new ContentPanel();
		final BorderLayoutData westData=new BorderLayoutData();
		westData.setCollapsible(true);
		westData.setCollapseMini(true);
		*/
		
		final ContentPanel center=new ContentPanel();
		final BorderLayoutData centerData=new BorderLayoutData();
		centerData.setCollapsible(false);
		centerData.setCollapseMini(false);
		
		center.add(createTab());
		
		
		final BorderLayoutContainer con=new BorderLayoutContainer();
		con.setCenterWidget(center,centerData);
		con.setBorders(false);
		//con.setWestWidget(west,westData);
		RootPanel.get().add(con);
		
		/* only tab version
		Widget tab=createTab();
		RootPanel.get().add(tab);
		 */
	
	}

	private Widget createTab() {
		final TabPanel tp=new TabPanel();
		tp.add(createForm(),"SawMovie");
		return tp;
	}

	private Widget createForm() {
		DataServiceAsync ds=GWT.create(DataService.class);
		
		FramedPanel panel=new FramedPanel();
		panel.setHeadingText("existing movie watched form");
		panel.setWidth("100%");
		
		VerticalLayoutContainer p=new VerticalLayoutContainer();
		panel.add(p);
		
		TbIdPersonProperties props = GWT.create(TbIdPersonProperties.class);
	    final ListStore<TbIdPerson> store = new ListStore<TbIdPerson>(props.key());
	    ds.getAllPersons(new AsyncCallback<List<TbIdPerson>>() {
			@Override
			public void onSuccess(List<TbIdPerson> result) {
				store.addAll(result);
			}
			@Override
			public void onFailure(Throwable caught) {
				Info.display("error", caught.toString());
			}
		});
	    
		ComboBox<TbIdPerson> personCombo=new ComboBox<TbIdPerson>(store,props.fullNameLabel());
		personCombo.addValueChangeHandler(new ValueChangeHandler<TbIdPerson>() {
			
			@Override
			public void onValueChange(ValueChangeEvent<TbIdPerson> event) {
				Info.display("Selected","You selected "+event.getValue());
			}
		});
		personCombo.setForceSelection(true);
		personCombo.setAllowBlank(false);
		
		p.add(new FieldLabel(personCombo, "Person"), new VerticalLayoutData(1, -1));
		return panel;
	}
}
