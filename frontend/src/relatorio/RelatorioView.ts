import { Chart } from "chart.js/auto";
import { ErroRelatorio } from "../infra/ErroRelatorio.js";
import { AutenticadorUtils } from "../Utils/AutenticadorUtils.js";
import { IRelatorioView } from "./interface/IRelatorioView.js";
import { RelatorioController } from "./RelatorioController.js";

export class RelatorioView implements IRelatorioView {
    private controller: RelatorioController;
    private autenticador = new AutenticadorUtils();
    private graficoLocacoes: Chart | null = null;
    private graficoTopItens: Chart | null = null;
    constructor() {
        this.controller = new RelatorioController(this);
        this.configurarEventos();
    }

    async configurarEventos(): Promise<void> {
        await this.preencherDataInicial();
        await this.preencherDataFinal();
        document
            .getElementById("gerar-relatorio")
            ?.addEventListener("click", (event) => {
                event.preventDefault();
                this.exibirGraficoRelatorioLocacaoesNoPeriodo();
                this.exibirGraficoRelatorioTopItens();
            });
    }

    async preencherDataInicial(): Promise<void> {
        const dataInicio = document.getElementById(
            "data-inicial",
        ) as HTMLInputElement;
        if (dataInicio) {
            const dataAtual = new Date();
            const ano = dataAtual.getFullYear();
            const mes = String(dataAtual.getMonth() + 1).padStart(2, "0");
            dataInicio.value = `${ano}-${mes}-01`;
        } else {
            throw new ErroRelatorio("Data Inicial invalida");
        }
    }

    async preencherDataFinal(): Promise<void> {
        const dataFim = document.getElementById(
            "data-final",
        ) as HTMLInputElement;
        if (dataFim) {
            const dataAtual = new Date();
            const ano = dataAtual.getFullYear();
            const mes = dataAtual.getMonth() + 1;
            const ultimoDia = new Date(ano, mes, 0).getDate();
            const mesStr = String(mes).padStart(2, "0");
            const diaStr = String(ultimoDia).padStart(2, "0");
            dataFim.value = `${ano}-${mesStr}-${diaStr}`;
        } else {
            throw new ErroRelatorio("Data Final invalida");
        }
    }
    getDataInicial(): string {
        const dataInicial = document.getElementById(
            "data-inicial",
        ) as HTMLInputElement;
        if (!dataInicial || !dataInicial.value) {
            throw new ErroRelatorio("Data Inicial invalida");
        }
        const dataConvertida = new Date(dataInicial.value);
        const dataFormatada = dataConvertida.toISOString().slice(0, 10);
        return dataFormatada;
    }
    getDataFinal(): string {
        const dataFinal = document.getElementById(
            "data-final",
        ) as HTMLInputElement;
        if (!dataFinal || !dataFinal.value) {
            throw new ErroRelatorio("Data Final invalida");
        }
        const dataConvertida = new Date(dataFinal.value);
        const dataFormatada = dataConvertida.toISOString().slice(0, 10);
        return dataFormatada;
    }
    async exibirGraficoRelatorioLocacaoesNoPeriodo(): Promise<void> {
        if (
            this.autenticador.getCargo() != "MECANICO" &&
            this.autenticador.getCargo() != "ATENDENTE"
        ) {
            const dataInicial = this.getDataInicial();
            const dataFinal = this.getDataFinal();

            try {
                if (this.graficoLocacoes) {
                    this.graficoLocacoes.destroy();
                    this.graficoLocacoes = null;
                }
                const dadosLocacoes =
                    await this.controller.obterLocacoesNoPeriodo(
                        dataInicial,
                        dataFinal,
                    );
                const graficoLocacoes = document.getElementById(
                    "grafico-locacoes",
                ) as HTMLCanvasElement;
                if (!graficoLocacoes) {
                    throw new ErroRelatorio(
                        "Elemento para renderização de locações não encontrado",
                    );
                }
                const nomes: string[] = dadosLocacoes.map((item: any) => {
                    const [ano, mes, dia] = item.data.split("-");
                    const dataLocal = new Date(
                        parseInt(ano),
                        parseInt(mes) - 1,
                        parseInt(dia),
                    );
                    return dataLocal.toLocaleDateString("pt-BR");
                });
                const dados: number[] = dadosLocacoes.map((item: any) =>
                    Number(item.valor_pago),
                );
                this.graficoLocacoes = new Chart(graficoLocacoes, {
                    type: "bar",
                    data: {
                        labels: nomes,
                        datasets: [
                            {
                                label: "Valor Pago nas Devoluções",
                                data: dados,
                                backgroundColor: "#36A2EB",
                                borderColor: "#36A2EB",
                                borderWidth: 1,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                },
                            },
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: "Valores Pagos nas Devoluções por Data",
                            },
                        },
                    },
                });
            } catch (error) {
                throw new ErroRelatorio(
                    "Erro ao gerar relatório de locações: " + error,
                );
            }
        }
    }
    async exibirGraficoRelatorioTopItens(): Promise<void> {
        if (this.autenticador.getCargo() != "MECANICO") {
            const dataInicial = this.getDataInicial();
            const dataFinal = this.getDataFinal();
            if (dataInicial > dataFinal) {
                alert("Data Inicial não pode ser maior que Data Final");
                return;
            }

            try {
                if (this.graficoTopItens) {
                    this.graficoTopItens.destroy();
                    this.graficoTopItens = null;
                    this.limparTabelaTopItens();
                }
                let dadosTopItens =
                    await this.controller.obterTopItensNoPeriodo(
                        dataInicial,
                        dataFinal,
                    );
                const graficoTopItens = document.getElementById(
                    "grafico-top-itens",
                ) as HTMLCanvasElement;
                if (!graficoTopItens) {
                    throw new ErroRelatorio(
                        "Elemento para renderização do gráfico de top itens não encontrado",
                    );
                }
                dadosTopItens = dadosTopItens.sort(
                    (a: any, b: any) => b.quantidade - a.quantidade,
                );
                let top10 = dadosTopItens.slice(0, 10);
                let outros = dadosTopItens.slice(10);
                let total = dadosTopItens.reduce(
                    (acumulador: number, item: any) =>
                        acumulador + item.quantidade,
                    0,
                );
                if (outros.length > 0) {
                    const somaOutros = outros.reduce(
                        (acumulador: number, item: any) =>
                            acumulador + item.quantidade,
                        0,
                    );
                    top10.push({
                        modelo: "Outros",
                        codigo: "",
                        quantidade: somaOutros,
                    });
                }
                const nomes = top10.map((item: any) => item.modelo);
                const dados = top10.map((item: any) => item.quantidade);
                const cores = [
                    "#FF6384",
                    "#36A2EB",
                    "#FFCE56",
                    "#4BC0C0",
                    "#9966FF",
                    "#FF9F40",
                    "#FF6384",
                    "#C9CBCF",
                    "#4BC0C0",
                    "#AAAAAA",
                ];
                const EtiquetasComPorcentagem = top10.map((item: any) => {
                    const porcentagem = (
                        (item.quantidade / total) *
                        100
                    ).toFixed(1);
                    return `${item.modelo} (${porcentagem}%)`;
                });
                this.graficoTopItens = new Chart(graficoTopItens, {
                    type: "pie",
                    data: {
                        labels: EtiquetasComPorcentagem,
                        datasets: [
                            {
                                label: "Quantidade Alugada",
                                data: dados,
                                backgroundColor: cores.slice(0, nomes.length),
                                borderWidth: 1,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: "bottom",
                            },
                            title: {
                                display: true,
                                text: "Top 10 Itens Mais Alugados",
                            },
                        },
                    },
                });
                this.preencherTabelaTopItens(dadosTopItens);
            } catch (error) {
                throw new ErroRelatorio("Erro ao gerar relatório de top itens");
            }
        }
    }

    private preencherTabelaTopItens(dados: any[]): void {
        const tbody = document.getElementById(
            "tabela-top-itens",
        ) as HTMLTableSectionElement;
        if (!tbody) return;
        const top10 = dados.slice(0, 10);
        tbody.innerHTML = "";
        if (dados.length > 10) {
            const outros = dados.slice(10);
            if (outros.length > 0) {
                const somaOutros = outros.reduce(
                    (acumulador: number, item: any) =>
                        acumulador + item.quantidade,
                    0,
                );
                top10.push({
                    modelo: "Outros",
                    codigo: "Diversos",
                    quantidade: somaOutros,
                });
            }
        }
        top10.forEach((item, indice) => {
            const row = tbody.insertRow();
            row.innerHTML = `
                <td>${indice + 1}º</td>
                <td>${item.modelo}</td>
                <td>${item.codigo}</td>
                <td>${item.quantidade}</td>
            `;
        });
    }
    limparTabelaTopItens(): void {
        const tbody = document.getElementById(
            "tabela-top-itens",
        ) as HTMLTableSectionElement;
        if (!tbody) return;
        tbody.innerHTML = "";
    }
}
