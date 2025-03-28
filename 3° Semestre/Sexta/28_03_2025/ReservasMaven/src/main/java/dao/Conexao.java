package dao;

import java.sql.DriverManager;
import java.sql.SQLException;

import java.sql.Connection;

public abstract class Conexao{
    public static Connection conn;    
    
    public static synchronized Connection getConnection() {
    	try {
    		if(conn == null || conn.isClosed()) {    			
    			conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/locacao","root","root"
    					+ "");
    			System.out.println("Conexão Estabelecida com sucesso");    			
    			return conn;    			
    		}else {
    			// System.out.println("Conexão Aberta previamente, retornado a instância que já está em execução");
    			// System.out.println(conn);
    			return conn;
    		}
    	}catch(SQLException e) {
    		e.printStackTrace(); 
    		conn = null;
    	}
    	return conn;
    }
    
    public static synchronized void closeConnection() {
    	try {
    		if(conn != null && !conn.isClosed())
    		conn.close();
    		conn = null;
    		System.out.println("Conexão fechada com segurança");
    	}catch(Exception e) {
    		e.printStackTrace();
    	}
    }
}