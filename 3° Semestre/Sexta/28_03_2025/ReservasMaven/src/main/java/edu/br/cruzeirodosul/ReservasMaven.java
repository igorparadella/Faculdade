package edu.br.cruzeirodosul;

import dao.*;
import model.*;

public class ReservasMaven {

    public static void main(String[] args) {
        // Usando o Construtor vazio
        Hospede h = new Hospede();
        
        // Usando o Construtor All Argumentos
        Hospede h1 = new Hospede("Naruto Uzumaki","Av da Folha, 123","29291010",new Contato("naruto.uzumaki@konoha.com","1164471838"));
        
        ICRUD hospedes = new DAOHospede();
        
        hospedes.create(h1);
    }
}