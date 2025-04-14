 
public class Fila<T> extends EstruturaEstatica<T> {

    public Fila() {
        super(10); // capacidade padrão
    }

    public Fila(int capacidade) {
        super(capacidade);
    }

    public boolean enfileira(T elemento) {
        return this.adiciona(elemento);
    }

    public T espiar() {
        if (this.estaVazia()) {
            return null;
        }
        return this.elementos[0];
    }
}
