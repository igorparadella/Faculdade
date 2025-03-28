package dao;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

import model.*;

public class DAOHospede implements ICRUD<Hospede>{		
	
	public DAOHospede() {
		
	}

	@Override
	public void create(Hospede h) {		
		String insert = "INSERT INTO hospedes(nome_hospede,endereco_hospede,documento_hospede,telefone_hospede,email_hospede) VALUES(?,?,?,?,?)";
		try (PreparedStatement stmt = Conexao.getConnection().prepareStatement(insert)){												    			    		      
		    
			stmt.setString(1, h.getNome());
		    stmt.setString(2, h.getEndereco());
		    stmt.setString(3, h.getDocumento());
		    stmt.setString(4, h.getContato().getTelefone());
		    stmt.setString(5, h.getContato().getEmail());
		    stmt.executeUpdate();
		    System.out.println("Hóspede Adicionado com Sucesso");
		    
		}catch(Exception e) {
			System.err.println(e.getMessage());
			e.printStackTrace();				
		}		
	}

	@Override
	public void delete(Hospede h) {
		String delete = "DELETE FROM hospedes WHERE id_hospede = ?";
		try(PreparedStatement stmt = Conexao.getConnection().prepareStatement(delete)){
			stmt.setInt(1,h.getId());
			stmt.executeUpdate();
			System.out.println("Hóspede Removido com Sucesso");
		}catch(Exception e) {
			System.err.println(e.getMessage());
			e.printStackTrace();	
		}
		
	}

	@Override
	public void update(Hospede h) {	
		
	}

	@Override
	public Hospede findById(Hospede h) {	
		return null;
	}

	@Override
	public List<Hospede> getAll() {
		List<Hospede> hospedes = new ArrayList<Hospede>();
		String select = "SELECT id_hospede,nome_hospede,endereco_hospede,documento_hospede,telefone_hospede,email_hospede FROM hospedes";
    	try(PreparedStatement stmt = Conexao.getConnection().prepareStatement(select)){    				
			ResultSet rs = stmt.executeQuery();
			while(rs.next()) {
				Hospede h = new Hospede();
				h.setNome(rs.getString("nome_hospede"));
				h.setEndereco(rs.getString("endereco_hospede"));
				h.setDocumento(rs.getString("documento_hospede"));
				h.setContato(new Contato(rs.getString("email_hospede"),rs.getString("telefone_hospede")));
				h.setId(rs.getInt("id_hospede"));
				hospedes.add(h);
			}
			
		} catch (SQLException e) {
			hospedes = null;
			System.err.println(e.getMessage());
			e.printStackTrace();
		}
		return hospedes;
	}
    
}
