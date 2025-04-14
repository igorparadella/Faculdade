import java.util.LinkedList;
import java.util.Queue;

public class FilaAtendimento {
    private Queue<Pessoa> filaNormal = new LinkedList<>();
    private Queue<Pessoa> filaPreferencial = new LinkedList<>();
    private int contadorPreferencial = 0;

    public void adicionarPessoa(Pessoa p) {
        if (p.isPreferencial()) {
            filaPreferencial.add(p);
        } else {
            filaNormal.add(p);
        }
    }

    public Pessoa chamarProximo() {
        if (!filaPreferencial.isEmpty() && (contadorPreferencial < 3 || filaNormal.isEmpty())) {
            contadorPreferencial++;
            return filaPreferencial.poll();
        } else if (!filaNormal.isEmpty()) {
            contadorPreferencial = 0;
            return filaNormal.poll();
        } else if (!filaPreferencial.isEmpty()) {
            return filaPreferencial.poll();
        }
        return null;
    }

    public boolean temPessoasNaFila() {
        return !filaNormal.isEmpty() || !filaPreferencial.isEmpty();
    }

    public void mostrarFilas() {
        System.out.println("Fila Preferencial: " + filaPreferencial);
        System.out.println("Fila Normal: " + filaNormal);
    }
}
