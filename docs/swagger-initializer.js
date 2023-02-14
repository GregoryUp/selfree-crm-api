window.onload = function() {
  //<editor-fold desc="Changeable Configuration Block">

  // the following lines will be replaced by docker/configurator, when it runs in a docker-container
  window.ui = SwaggerUIBundle({
    //url: "/swagger.json",
    urls: [
      {
        url: "/swagger-json/clients.json",
        name: "Клиенты"
      },
      {
        url: "/swagger-json/leads.json",
        name: "Лиды"
      },
      {
        url: "/swagger-json/teachers.json",
        name: "Преподаватели"
      },
      {
        url: "/swagger-json/tariffs.json",
        name: "Тариффы и абонементы"
      },
      {
        url: "/swagger-json/lessons.json",
        name: "Уроки"
      },
      {
        url: "/swagger-json/payments.json",
        name: "Платежи"
      }
    ],
    dom_id: '#swagger-ui',
    deepLinking: true,
    presets: [
      SwaggerUIBundle.presets.apis,
      SwaggerUIStandalonePreset
    ],
    plugins: [
      SwaggerUIBundle.plugins.DownloadUrl
    ],
    layout: "StandaloneLayout"
  });

  //</editor-fold>
};
