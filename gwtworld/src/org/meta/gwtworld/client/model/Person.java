package org.meta.gwtworld.client.model;

import java.io.Serializable;

@SuppressWarnings("serial")
public class Person implements Serializable{
	private int id;
	private String name;
	private String lastname;
	
	// this must be declared so the class could be serialized
	public Person() {
	}
	public Person(int id,String name,String lastname) {
		this.id=id;
		this.name=name;
		this.lastname=lastname;
	}
	public String getName() {
		return name;
	}
	public void setName(String name) {
		this.name = name;
	}
	public String getLastname() {
		return lastname;
	}
	public void setLastname(String lastname) {
		this.lastname = lastname;
	}
	public int getId() {
		return id;
	}
	public void setId(int id) {
		this.id = id;
	}
	
	// more methods
	public String getFullName() {
		return name+" "+lastname;
	}
}
