import { useEffect, useState } from "react";

const EVENT_NAME = "EAZYDOCS_ROUTE_CHANGE";
const routeEvent = new Event(EVENT_NAME);

const processHref = (href: string) => {
    const url = new URL(href);
    const query: Record<string, string> = {};

    url.searchParams.forEach((value, key) => {
        query[key] = value;
    });

    return {
        pathname: url.pathname,
        query,
    };
};

export function useRoute() {
    const url = processHref(window.location.href);

    const [pathname, setPathname] = useState(url.pathname);
    const [query, setQuery] = useState(url.query);

    const navigate = (url: string) => {
        window.history.pushState({}, "", url);
        window.dispatchEvent(routeEvent);
    };

    const updateQuery = (newQuery: Record<string, string>, clean = false) => {
        const nextQuery = clean ? {} : { ...query };

        for (const key in newQuery) {
            const value = newQuery[key];

            if (value === undefined) continue;

            if (value === "" || value === null) {
                delete nextQuery[key];
                continue;
            }

            nextQuery[key] = value;
        }

        const searchParams = new URLSearchParams(nextQuery).toString();
        const newUrl = `${pathname}?${searchParams}`;

        navigate(newUrl);
    };

    useEffect(() => {
        const handleRouteChange = () => {
            const href = processHref(window.location.href);
            setPathname(href.pathname);
            setQuery(href.query);
        };

        window.addEventListener(EVENT_NAME, handleRouteChange);
        return () => {
            window.removeEventListener(EVENT_NAME, handleRouteChange);
        };
    }, [pathname, query]);

    return { navigate, pathname, query, updateQuery };
}
