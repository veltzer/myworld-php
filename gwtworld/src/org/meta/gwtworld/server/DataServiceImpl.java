package org.meta.gwtworld.server;

import java.util.ArrayList;
import java.util.List;

import javax.persistence.EntityManager;
import javax.persistence.EntityManagerFactory;
import javax.persistence.Persistence;
import javax.persistence.Query;
import javax.persistence.TypedQuery;
import javax.persistence.criteria.CriteriaBuilder;
import javax.persistence.criteria.CriteriaQuery;
import javax.persistence.criteria.Predicate;
import javax.persistence.criteria.Root;

import org.meta.gwtworld.client.DataService;
import org.meta.gwtworld.client.model.TbIdPerson;

import com.google.gwt.user.server.rpc.RemoteServiceServlet;

/**
 * The server side implementation of the RPC service.
 */
@SuppressWarnings("serial")
public class DataServiceImpl extends RemoteServiceServlet implements
		DataService {

	@Override
	public List<TbIdPerson> getPersons() throws IllegalArgumentException {
		List<TbIdPerson> ret=new ArrayList<TbIdPerson>();
		TbIdPerson newp=new TbIdPerson();
		newp.setFirstname("Mark");
		newp.setSurname("veltzer");
		ret.add(newp);
		return ret;
	}
	public List<TbIdPerson> getAllPersons() throws IllegalArgumentException {
		EntityManagerFactory factory=Persistence.createEntityManagerFactory("gwtworld");
		EntityManager em=factory.createEntityManager();
		CriteriaBuilder qb=em.getCriteriaBuilder();
		CriteriaQuery<TbIdPerson> c=qb.createQuery(TbIdPerson.class);
		Root<TbIdPerson> p=c.from(TbIdPerson.class);
		Predicate condition=qb.equal(p.get("firstname"), "Mark");
		c.where(condition);
		TypedQuery<TbIdPerson> tq=em.createQuery(c);
		return tq.getResultList();
	}

	@SuppressWarnings("unchecked")
	public List<TbIdPerson> getAllPersonsQuery() throws IllegalArgumentException {
		EntityManagerFactory factory=Persistence.createEntityManagerFactory("gwtworld");
		EntityManager em=factory.createEntityManager();
		Query q=em.createQuery("select t from TbIdPerson t");
		return q.getResultList();
	}
	public static void printIt(List<TbIdPerson> l) {
		for(TbIdPerson i:l) {
			System.out.println(i);
		}
		System.out.println("Size: "+l.size());
	}
	public static void main(String[] args) {
		DataServiceImpl dsi=new DataServiceImpl();
		printIt(dsi.getPersons());
		printIt(dsi.getAllPersons());
		printIt(dsi.getAllPersonsQuery());
	}
}
