package org.meta.gwtworld.client;

import java.util.List;

import org.meta.gwtworld.client.model.Person;

import com.google.gwt.user.client.rpc.RemoteService;
import com.google.gwt.user.client.rpc.RemoteServiceRelativePath;

/**
 * The client side stub for the RPC service.
 */
@RemoteServiceRelativePath("DataService")
public interface DataService extends RemoteService {
	List<Person> getPersons() throws IllegalArgumentException;
}
