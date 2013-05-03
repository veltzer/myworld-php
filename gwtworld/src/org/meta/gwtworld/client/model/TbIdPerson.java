package org.meta.gwtworld.client.model;

import java.io.Serializable;
import javax.persistence.*;


/**
 * The persistent class for the TbIdPerson database table.
 * 
 */
@Entity
@Table(name="TbIdPerson")
public class TbIdPerson implements Serializable {
	@Override
	public String toString() {
		return firstname+ " "+surname;
	}
	public String getFullname() {
		return toString();
	}
	private static final long serialVersionUID = 1L;

	@Id
	private int id;

	private String firstname;

	private int honorificId;

	private int ordinal;

	private String othername;

	private String remark;

	private String surname;

	public TbIdPerson() {
	}

	public int getId() {
		return this.id;
	}

	public void setId(int id) {
		this.id = id;
	}

	public String getFirstname() {
		return this.firstname;
	}

	public void setFirstname(String firstname) {
		this.firstname = firstname;
	}

	public int getHonorificId() {
		return this.honorificId;
	}

	public void setHonorificId(int honorificId) {
		this.honorificId = honorificId;
	}

	public int getOrdinal() {
		return this.ordinal;
	}

	public void setOrdinal(int ordinal) {
		this.ordinal = ordinal;
	}

	public String getOthername() {
		return this.othername;
	}

	public void setOthername(String othername) {
		this.othername = othername;
	}

	public String getRemark() {
		return this.remark;
	}

	public void setRemark(String remark) {
		this.remark = remark;
	}

	public String getSurname() {
		return this.surname;
	}

	public void setSurname(String surname) {
		this.surname = surname;
	}

}