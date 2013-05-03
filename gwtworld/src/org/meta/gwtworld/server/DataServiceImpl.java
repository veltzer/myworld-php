package org.meta.gwtworld.server;

import java.util.ArrayList;
import java.util.List;

import org.meta.gwtworld.client.DataService;
import org.meta.gwtworld.client.model.Person;
import com.google.gwt.user.server.rpc.RemoteServiceServlet;

/**
 * The server side implementation of the RPC service.
 */
@SuppressWarnings("serial")
public class DataServiceImpl extends RemoteServiceServlet implements
		DataService {

	@Override
	public List<Person> getPersons() throws IllegalArgumentException {
		List<Person> ret=new ArrayList<Person>();
		ret.add(new Person(1,"Mark","Veltzer"));
		return ret;
	}

}
