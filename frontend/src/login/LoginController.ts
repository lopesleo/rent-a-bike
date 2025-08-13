import { ILoginView } from "./interface/ILoginView.js";
import { LoginManager } from "./LoginManager.js";

export class LoginController {
    private manager: LoginManager = new LoginManager();
    constructor(private view: ILoginView) {}

    async realizarLogin(user: string, senha: string): Promise<boolean> {
        try {
            await this.manager.realizarLogin(user, senha);
            return true;
        } catch (erro: unknown) {
            return false;
        }
    }
}
