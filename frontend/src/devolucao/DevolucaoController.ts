const ApiURL = import.meta.env.VITE_URL;

import { ErroDevolucao } from "../infra/ErroDevolucao";
import { ErroFuncionario } from "../infra/ErroFuncionario";
import { Item } from "../item/Item";
import { DevolucaoManager, PrecoItem } from "./DevolucaoManager";
import { IDevolucaoView } from "./interface/IDevolucaoView";

interface DevolucaoEmCurso {
    locacaoId: number;
    clienteNome: string;
    clienteCpf: string;
    dataHoraEntregaPrevista: string;
    locacaoHoras: number;
    itens: Item[];
}

export class DevolucaoController {
    constructor(
        private view: IDevolucaoView,
        private manager: DevolucaoManager,
    ) {
        this.inicializaDevolucao();
        this.carregarFuncionarios();
    }

    private async inicializaDevolucao(): Promise<void> {
        try {
            const raw = localStorage.getItem("devolucaoEmCurso");
            if (!raw) {
                setTimeout(() => {
                    alert("Nenhuma devolução em andamento.");
                    window.location.assign("/listarlocacao");
                }, 50);
                return;
            }

            let devolucaoData: DevolucaoEmCurso;
            try {
                devolucaoData = JSON.parse(raw) as DevolucaoEmCurso;
            } catch {
                alert("Erro ao ler dados da devolução em andamento.");
                window.location.assign("/listarlocacao");
                return;
            }

            let precos: PrecoItem[];
            try {
                precos = await this.manager.carregaValorHoraItemLocacao(
                    devolucaoData.locacaoId,
                );
            } catch (e) {
                if (e instanceof ErroDevolucao) {
                    alert(e.message);
                } else {
                    alert("Erro inesperado ao carregar valores dos itens.");
                }
                window.location.assign("/listarlocacao");
                return;
            }

            devolucaoData.itens.forEach((i) => {
                const p = precos.find((j) => j.id === i.id);
                if (p) i.valor_hora = p.valorHora;
            });

            const dataHoraDevolucao = new Date().toLocaleString("pt-BR", {
                timeZone: "America/Sao_Paulo",
            });
            this.view.exibirDevolucao(
                devolucaoData.locacaoId,
                devolucaoData.locacaoHoras,
                devolucaoData.clienteNome,
                devolucaoData.clienteCpf,
                devolucaoData.dataHoraEntregaPrevista,
                dataHoraDevolucao,
                devolucaoData.itens,
            );

            const dataPrevista = devolucaoData.dataHoraEntregaPrevista;
            const dataDevol = new Date().toISOString();

            const horasOriginais = devolucaoData.locacaoHoras;
            const horasExtras = DevolucaoController.calcularHorasExtras(
                dataPrevista,
                dataDevol,
            );
            const totalHoras = horasOriginais + horasExtras;

            const valorItens = devolucaoData.itens.reduce(
                (acc, i) => acc + i.valor_hora * totalHoras,
                0,
            );

            const desconto = totalHoras > 2 ? valorItens * 0.1 : 0;
            const valorTotal = valorItens - desconto;

            this.view.exibirResumo(
                totalHoras,
                horasExtras,
                desconto,
                valorTotal,
            );
            this.view.atualizarItensEResumo(
                devolucaoData.itens,
                totalHoras,
                desconto,
                valorTotal,
            );

            this.view.getForm().addEventListener("submit", (e) => {
                e.preventDefault();
                this.enviarDevolucao();
            });
        } catch (e) {
            console.log(e);
            alert(
                "Erro inesperado ao inicializar a devolução. " +
                    (e as Error).message,
            );
            window.location.assign("/listarlocacao");
        }
    }
    /**
     * Envia a devolução para o backend.
     */
    private async enviarDevolucao(): Promise<void> {
        try {
            const raw = localStorage.getItem("devolucaoEmCurso");
            if (!raw) {
                alert("Dados da devolução não encontrados.");
                return;
            }

            let devolucaoData: DevolucaoEmCurso;
            try {
                devolucaoData = JSON.parse(raw) as DevolucaoEmCurso;
            } catch {
                alert("Erro ao ler dados da devolução.");
                return;
            }

            const itensComAvarias = this.view.getItensComAvarias();

            const payload = {
                locacao_id: devolucaoData.locacaoId,
                funcionario_id: this.view.getFuncionarioId(),
                itens: devolucaoData.itens.map((item) => {
                    const entry = itensComAvarias.find((a) => a.id === item.id);
                    return {
                        item_id: item.id,
                        avarias: entry ? entry.avarias : [],
                        limpeza_aplicada: this.view.isLimpezaAplicada(item.id),
                    };
                }),
            };

            if (payload.funcionario_id === 0) {
                alert("Funcionário não autenticado.");
                return;
            }

            await this.manager.enviarDevolucao(payload);
            localStorage.removeItem("devolucaoEmCurso");
            alert("Devolução registrada com sucesso!");
            window.location.href = "/listardevolucao";
        } catch (err) {
            if (
                err instanceof ErroDevolucao ||
                err instanceof ErroFuncionario
            ) {
                alert(err.message);
            } else {
                alert("Erro ao registrar devolução: " + (err as Error).message);
            }
        }
    }

    /**
     * Calcula se houve atraso (mais de 15 minutos)
     */
    public static calcularHorasExtras(
        previstoStr: string,
        devolucaoStr: string,
    ): number {
        const previsto = new Date(previstoStr.replace(" ", "T"));
        const devolucao = new Date(devolucaoStr);

        const diferencaMs = devolucao.getTime() - previsto.getTime();
        const diferencaMin = diferencaMs / 1000 / 60;

        if (diferencaMin <= 15) return 0;

        return Math.ceil(diferencaMin / 60);
    }

    public async carregarFuncionarios(): Promise<
        Array<{ id: number; nome: string }>
    > {
        try {
            const funcionarios = await this.manager.carregarFuncionarios();
            return funcionarios.map((f) => {
                return { id: f.id, nome: f.nome };
            });
        } catch (e) {
            if (e instanceof ErroFuncionario) {
                alert(e.message);
            } else {
                alert("Erro desconhecido ao carregar funcionários.");
            }
            return [];
        }
    }

    //popula funcionaios select da view

    public async preencherFuncionarios(): Promise<void> {
        try {
            await this.carregarFuncionarios();
        } catch (e) {
            if (e instanceof ErroFuncionario) {
                alert(e.message);
            } else {
                alert("Erro desconhecido ao carregar funcionários.");
            }
        }
    }
}
