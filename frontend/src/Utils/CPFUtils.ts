export function DevolveCPFMascarado(cpf: string): string {
    if (!cpf) return "";
    const cpfSemMascara = cpf.replace(/\D/g, "");
    if (cpfSemMascara.length !== 11) return "";
    return `${cpfSemMascara.slice(0, 3)}.${cpfSemMascara.slice(
        3,
        6,
    )}.${cpfSemMascara.slice(6, 9)}-${cpfSemMascara.slice(9)}`;
}
export function DevolveCPFSemMascara(cpf: string): string {
    if (!cpf) return "";
    const cpfSemMascara = cpf.replace(/\D/g, "");
    if (cpfSemMascara.length !== 11) return "";
    return cpfSemMascara;
}
