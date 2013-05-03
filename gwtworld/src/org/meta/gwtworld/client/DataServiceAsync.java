package org.meta.gwtworld.client;

import java.util.List;

import org.meta.gwtworld.client.model.Person;

import com.google.gwt.user.client.rpc.AsyncCallback;

/**
 * The async counterpart of <code>GreetingService</code>.
 */
public interface DataServiceAsync {
	void getPersons(AsyncCallback<List<Person>> callback)
			throws IllegalArgumentException;
}
