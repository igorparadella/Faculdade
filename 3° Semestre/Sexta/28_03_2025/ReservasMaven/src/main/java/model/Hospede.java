package model;

import lombok.*;

@Data
@Getter
@Setter
@AllArgsConstructor
@NoArgsConstructor
@RequiredArgsConstructor
public class Hospede {
		
    private int id;	
    
    @NonNull
    private String nome;
    
    @NonNull
    private String endereco;
    
    @NonNull
    private String documento;
    
    @NonNull
    private Contato contato;

    public void exibeDados(){
        System.out.println("Nome do Hóspede: "+this.nome);
        System.out.println("Endereço do Hóspede: "+this.endereco);
        System.out.println("Documento do Hóspede: "+this.documento);
        //System.out.println("Telefone do Hóspede: "+this.contato.telefone);
        //System.out.println("Email do Hóspede: "+this.contato.email);
    } 			
}