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
        System.out.println("Nome do H�spede: "+this.nome);
        System.out.println("Endere�o do H�spede: "+this.endereco);
        System.out.println("Documento do H�spede: "+this.documento);
        //System.out.println("Telefone do H�spede: "+this.contato.telefone);
        //System.out.println("Email do H�spede: "+this.contato.email);
    } 			
}