export class ErroDominio extends Error {
    private problemas: string[] = [];

    public constructor(message?: string) {
        super(message);
    }

    static comProblemas(problemas: string[]): ErroDominio {
        const e = new ErroDominio();
        e.setProblemas(problemas);
        return e;
    }

    public setProblemas(problemas: string[]) {
        this.problemas = problemas;
    }

    public getProblemas(): string[] {
        return this.problemas;
    }
    public static isErroDominio(e: unknown): e is ErroDominio {
        return e instanceof ErroDominio;
    }
}
