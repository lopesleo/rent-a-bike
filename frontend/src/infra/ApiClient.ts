const ApiURL = "/api";
const defaultOptions: RequestInit = {
    credentials: "include",
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
    },
};

async function apiFetch(
    endpoint: string,
    options: RequestInit = {},
): Promise<any> {
    const mergedOptions: RequestInit = {
        ...defaultOptions,
        ...options,
        headers: {
            ...defaultOptions.headers,
            ...options.headers,
        },
    };

    const response = await fetch(`${ApiURL}${endpoint}`, mergedOptions);
    const data = await response.json();

    if (!response.ok) {
        const errorMessage =
            data.mensagem || `Erro na requisição para ${endpoint}`;
        throw new Error(errorMessage);
    }

    return data;
}

export const ApiClient = {
    get: (endpoint: string) => apiFetch(endpoint),
    post: (endpoint: string, body: any) => {
        return apiFetch(endpoint, {
            method: "POST",
            body: JSON.stringify(body),
        });
    },
};
