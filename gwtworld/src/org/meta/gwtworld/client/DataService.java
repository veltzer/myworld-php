package org.meta.gwtworld.client;

import java.util.List;

import org.meta.gwtworld.client.model.TbIdPerson;

import com.google.gwt.user.client.rpc.RemoteService;
import com.google.gwt.user.client.rpc.RemoteServiceRelativePath;

/**
 * The client side stub for the RPC service.
 */
@RemoteServiceRelativePath("DataService")
public interface DataService extends RemoteService {
	List<TbIdPerson> getPersons() throws IllegalArgumentException;
	List<TbIdPerson> getAllPersons() throws IllegalArgumentException;
}
