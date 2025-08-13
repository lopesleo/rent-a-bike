import { ApiClient } from "../infra/ApiClient";
import { ErroLogin } from "../infra/ErroLogin";

export class LoginManager {
    async realizarLogin(usuario: string, senha: string): Promise<boolean> {
        try {
            const dadosFuncionario = await ApiClient.post("/login", {
                usuario: usuario,
                senha: senha,
            });

            sessionStorage.setItem(
                "funcionario",
                JSON.stringify(dadosFuncionario),
            );
            return true;
        } catch (error: unknown) {
            if (error instanceof Error) {
                throw new ErroLogin(error.message);
            }
            throw new ErroLogin(
                "Ocorreu um erro desconhecido durante o login.",
            );
        }
    }
}
