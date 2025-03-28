package dao;

import java.util.List;

public interface ICRUD<T>{

	public void create(T t);
	
	public void delete(T t);
	
	public void update(T t);
	
	public T findById(T t);
	
	public List<T> getAll();
	
}
