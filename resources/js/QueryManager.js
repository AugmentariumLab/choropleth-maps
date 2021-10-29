import {API_ENDPOINTS} from "./ApiEndpoints";

/**
 * This is a query manager. The main purpose is to send queries to the server.
 * In the future, I may add some form of client-side caching to improve performance.
 */
export class QueryManager {
    /**
     * constructor - Instantiate a QueryManager object. Currently does nothing.
     *
     * @return {type}  description
     */
    constructor() {}

    /**
     * queryByZip - Query features by zip code.
     *
     * @param  {type} queryParams description
     * @return {type}             description
     */
    queryByZip(queryParams) {
        const queryUrl = new URL(API_ENDPOINTS.BY_ZIP);
        Object.keys(queryParams).forEach(key =>
            queryUrl.searchParams.append(key, queryParams[key])
        );
        return fetch(queryUrl).then(response => response.json());
    }
}
