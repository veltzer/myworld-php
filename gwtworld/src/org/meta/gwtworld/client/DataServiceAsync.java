package org.meta.gwtworld.client;

import java.util.List;

import org.meta.gwtworld.client.model.TbIdPerson;

import com.google.gwt.user.client.rpc.AsyncCallback;

/**
 * The async counterpart of <code>GreetingService</code>.
 */
public interface DataServiceAsync {
	void getPersons(AsyncCallback<List<TbIdPerson>> callback)
			throws IllegalArgumentException;
	void getAllPersons(AsyncCallback<List<TbIdPerson>> callback)
			throws IllegalArgumentException;
}
