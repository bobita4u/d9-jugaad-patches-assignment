<?php

namespace Drupal\qrcode_generator\Plugin\Block;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Endroid\QrCode\QrCode;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\node\NodeInterface;

/**
 * Provides a 'QR Code Generator' block.
 *
 * @Block(
 *   id = "qrcode_generator_block",
 *   admin_label = @Translation("QR Code Generator Block"),
 *   category = @Translation("Jugaad Patches")
 * )
 */
class GenerateQRCode extends BlockBase implements ContainerFactoryPluginInterface
{
  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The Node entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $nodeTypeManager;

  /**
   * Constructs a new QrCodeGeneratorBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param RouteMatchInterface $route_match
   *   The current route match.
   * @param RequestStack $request_stack
   * @param EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match, RequestStack $request_stack, EntityTypeManagerInterface $entity_type_manager)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
    $this->requestStack = $request_stack;
    $this->nodeTypeManager = $entity_type_manager->getStorage('node_type');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('request_stack'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build()
  {
    // Check if we're on a node page.
    if ($this->routeMatch->getRouteName() == 'entity.node.canonical') {
      // Get the node ID from the route parameters.
      $node_id = $this->routeMatch->getParameter('node');
      if ($node_id instanceof NodeInterface) {
        // Get the node type.
        $node_type = $node_id->getType();

        // You can further customize this block's behavior based on the node type.
        if ($node_type == 'jugaad_product') {
          // Load a specific field from the node.
          $app_purchase_link = $node_id->get('field_app_purchase_link')->getValue();

          if (!empty($app_purchase_link) && isset($app_purchase_link[0]['uri']) && UrlHelper::isValid($app_purchase_link[0]['uri'])) {
            $link = $app_purchase_link[0]['uri'];
            $qrCode = new QrCode($link);

            // Set QR code options if needed.
            $qrCode->setSize(300);
            $qrCode->setMargin(10);

            $imageData = $qrCode->writeDataUri(); // Use writeDataUri() to get the data URI format.

            // Use the theme function to render the QR code image.
            $output = [
              '#theme' => 'qr_code_image',
              '#image_data' => $imageData,
              '#cache' => [
                'tags' => ['node:' . $node_id->id()],
                'contexts' => ['url.path'],
              ],
            ];

            return $output;
          }
        }
      }
    }

    return [];
  }
}
