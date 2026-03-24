import { defineRouteMiddleware } from "@astrojs/starlight/route-data";

export const onRequest = defineRouteMiddleware((context) => {
    const { starlightRoute } = context.locals;

    if (starlightRoute.toc?.items && starlightRoute.toc?.items.length < 2) {
        starlightRoute.toc = undefined;
    } else {
        // Remove "Overview" item.
        starlightRoute.toc?.items.shift();
        // const overviewItem = starlightRoute.toc?.items[0];
        // if (overviewItem) {
        //     overviewItem.text = "Back to top";
        //     starlightRoute.toc?.items.push(starlightRoute.toc?.items.shift())
        // }

    }
});
